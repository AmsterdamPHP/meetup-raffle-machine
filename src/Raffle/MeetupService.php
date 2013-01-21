<?php

namespace Raffle;

class MeetupService
{
    /**
     * Group name.
     */
    const GROUP = 'amsterdamphp';

    /**
     * Meetup connection
     *
     * @var MeetupConnection
     */
    protected $connection;

    /**
     * Constructor. Sets dependencies.
     *
     * @param \MeetupConnection $connection
     * @return void
     */
    public function __construct(\MeetupConnection $connection)
    {
        $this->connection = $connection;
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
                'group_urlname' => self::GROUP,
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
        $rsvps = $rsvpsRequest->getRsvps(array('event_id' => $id));

        // Intersect the RSVPs with the checkins and add them to the event array
        $checkedInMemberIds = array();
        foreach ($checkins as $checkin) {
            $checkedInMemberIds[] = $checkin['member_id'];
        }
        foreach ($rsvps as $rsvp) {
            if (in_array($rsvp['member']['member_id'], $checkedInMemberIds)) {
                $event['checkins'][] = array(
                    'id' => $rsvp['member']['member_id'],
                    'name' => $rsvp['member']['name'],
                    'photo' => $rsvp['member_photo']
                );
            }
        }

        return $event;
    }
}