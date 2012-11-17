<?php
namespace Raffle\Event;

class EventService
{
    /**
     * @var array
     */
    protected $meetupConfig;

    /**
     * @var \Raffle\User\UserService
     */
    protected $userService;

    /**
     * @var \Predis\Client
     */
    protected $redisApi;

    /**
     * @param array $meetupConfig
     * @param \Raffle\User\UserService $userService
     * @param \Predis\Client $redis
     */
    public function __construct($meetupConfig, $userService, $redis)
    {
        $this->meetupConfig = $meetupConfig;
        $this->userService  = $userService;
        $this->redisApi     = $redis;
    }

    /**
     * Loads a list of events for a given Meetup Group
     *
     * @param string $group
     * @param string $status
     *
     * @return EventEntity[]
     */
    public function loadEvents($group, $status = '')
    {
        $m = new \MeetupEvents($this->meetupConfig);

        $eventsData = $m->getEvents(array( 'group_urlname' => $group, 'status' => $status));

        return array_map(array($this, 'buildEventEntity'), $eventsData);
    }

    /**
     * Loads data for a Meetup Event
     *
     * @param string $id
     * @param bool $loadUsers
     *
     * @return EventEntity
     */
    public function loadEvent($id, $loadUsers = false)
    {
        $eventApi  = new \MeetupEvents($this->meetupConfig);
        $eventData = $eventApi->getEvent($id, array());

        $event = $this->buildEventEntity($eventData);

        if ($loadUsers) {
            $users = $this->userService->getEventUsers($event->getId());
            $event->setUsers($users);
        }

        return $event;
    }

    /**
     * Registers an user who is not in Meetup
     *
     * @param string $eventId
     * @param string $name
     * @param string $email
     *
     * @return boolean
     */
    public function addNonMeetupUser($eventId, $name, $email)
    {
        $user = $this->userService->createNonMeetupUser($name, $email);

        return $this->redisApi->rpush('event:' . $eventId, $user->getJson());
    }

    /**
     * Parses Meetup Data into Event data
     *
     * @param mixed $data
     * @return EventEntity
     */
    protected function buildEventEntity($data)
    {
        $event  = new EventEntity();
        $event->setId($data['id']);
        $event->setStatus($data['status']);
        $event->setVisibility($data['visibility']);
        $event->setVenue($data['venue']);
        $event->setDescription($data['description']);
        $event->setUrl($data['event_url']);
        $event->setRating($data['rating']);
        $event->setName($data['name']);
        $event->setDate(\DateTime::createFromFormat('U', ($data['time']/1000)));

        $rsvp = array(
            'yes'   => $data['yes_rsvp_count'],
            'maybe' => $data['maybe_rsvp_count'],
        );
        $event->setRsvp($rsvp);

        return $event;
    }
}
