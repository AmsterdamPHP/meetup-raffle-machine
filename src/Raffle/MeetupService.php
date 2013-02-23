<?php

namespace Raffle;

use Buzz\Client\Curl;
use Buzz\Message\Request;
use Buzz\Message\Response;

class MeetupService
{
    /**
     * Meetup connection
     *
     * @var MeetupConnection
     */
    protected $connection;

    /**
     * Meetup group
     *
     * @var string
     */
    protected $group;

    /**
     * Constructor. Sets dependencies.
     *
     * @param \MeetupConnection $connection
     * @param string $group
     * @return void
     */
    public function __construct(\MeetupConnection $connection, $group)
    {
        $this->connection = $connection;
        $this->group      = $group;
    }

    /**
     * Fetch all events in the past and up to a day in the future.
     *
     * @return array
     */
    public function getEvents()
    {
        $eventsRequest = new \MeetupEvents($this->connection);

        // Fetch past and future events (only upcoming contains the current event)
        $events = $eventsRequest->getEvents(
            array(
                'group_urlname' => $this->group,
                'status' => 'past,upcoming',
                'desc' => 'desc'
            )
        );

        // Filter out events further in the future than a day
        $dayFromNow = (time() + (24 * 60 * 60)) * 1000;
        return array_filter(
            $events,
            function($value) use ($dayFromNow) {
                if ($value['time'] < $dayFromNow) {
                    return true;
                }
            }
        );
    }

    /**
     * Get a single event.
     *
     * @param string $id
     * @return array
     */
    public function getEvent($id)
    {
        $eventsRequest = new \MeetupEvents($this->connection);
        $checkinsRequest = new \MeetupCheckins($this->connection);
        $rsvpsRequest = new \MeetupRsvps($this->connection);

        // Fetch, event, checkins and RSVPs (only the latter has pictures)
        $event = $eventsRequest->getEvent($id, array());
        $checkins = $checkinsRequest->getCheckins(array('event_id' => $id));
        $rsvps = $rsvpsRequest->getRsvps(array('event_id' => $id, 'rsvp' => 'yes', 'order' => 'name'));

        // Intersect the RSVPs with the checkins and add them to the event array
        $checkedInMemberIds = array();
        foreach ($checkins as $checkin) {
            $checkedInMemberIds[] = $checkin['member_id'];
        }
        $event['checkins'] = array();
        $event['rsvps']    = array();
        foreach ($rsvps as $rsvp) {
            if (in_array($rsvp['member']['member_id'], $checkedInMemberIds)) {
                $event['checkins'][] = array(
                    'id' => $rsvp['member']['member_id'],
                    'name' => $rsvp['member']['name'],
                    'photo' => $rsvp['member_photo']
                );
            }

            $event['rsvps'][] = array(
                'id'        => $rsvp['member']['member_id'],
                'name'      => $rsvp['member']['name'],
                'photo'     => $rsvp['member_photo'],
                'checkedIn' => in_array($rsvp['member']['member_id'], $checkedInMemberIds)
            );
        }

        return $event;
    }

    /**
     * Allows and Admin to check a user into an event
     *
     * API Client has no support for POST, so we use Buzz.
     *
     * @param string $eventId
     * @param string $userId
     * @return bool
     */
    public function checkUserIn($eventId, $userId)
    {
        $params = array(
            'event_id' => $eventId,
            'attendee_member_id' => $userId,
        );
        $params = $this->connection->modify_params($params);

        $url = MEETUP_API_URL . '/2/checkin';

        $request = new Request('POST');
        $request->fromUrl($url);
        $request->setContent($params);

        $response =  $this->sendPostRequest($request);

        if ($response->getStatusCode() != 201) {
            return false;
        }

        return true;
    }

    /**
     * @param $request
     * @return \Buzz\Message\Response
     */
    private function sendPostRequest($request)
    {
        $client = new Curl();

        $response = new Response();

        $client->send($request, $response);

        return $response;
    }
}
