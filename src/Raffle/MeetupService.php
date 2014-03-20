<?php

namespace Raffle;

use DMS\Service\Meetup\AbstractMeetupClient;
use Predis\Client;

class MeetupService
{
    /**
     * Meetup client
     *
     * @var AbstractMeetupClient
     */
    protected $client;

    /**
     * Meetup group
     *
     * @var string
     */
    protected $group;
    /**
     * @var \Predis\Client
     */
    private $cache;

    /**
     * Constructor. Sets dependencies.
     *
     * @param AbstractMeetupClient $client
     * @param string $group
     *
     * @return \Raffle\MeetupService
     */
    public function __construct(AbstractMeetupClient $client, $group, Client $cache)
    {
        $this->client = $client;
        $this->group  = $group;
        $this->cache = $cache;
    }

    /**
     * Fetch all events in the past and up to a day in the future.
     *
     * @return array
     */
    public function getEvents()
    {

        $cached = $this->getFromCache('events_cache');
        if ($cached !== null) {
            return $cached;
        }

        // Fetch past and future events (only upcoming contains the current event)
        $events = $this->client->getEvents(
            array(
                'group_urlname' => $this->group,
                'status' => 'past,upcoming',
                'desc' => 'desc'
            )
        );

        // Filter out events further in the future than a day
        $dayFromNow = (time() + (24 * 60 * 60)) * 1000;
        $events = $events->filter(function($value) use ($dayFromNow) {
                return ($value['time'] < $dayFromNow)? true : false;
        });

        $this->saveInCache('events_cache', $events);

        return $events;
    }

    /**
     * Get a single event.
     *
     * @param string $id
     * @return array
     */
    public function getEvent($id)
    {

        $cached = $this->getFromCache('event_cache_'.$id);
        if ($cached !== null) {
            return $cached;
        }

        // Fetch, event, checkins and RSVPs (only the latter has pictures)
        $event    = $this->client->getEvent(array('id' => $id));
        $checkins = $this->client->getCheckins(array('event_id' => $id));
        $rsvps    = $this->client->getRSVPs(array('event_id' => $id, 'rsvp' => 'yes', 'order' => 'name'));

        // Intersect the RSVPs with the checkins and add them to the event array
        $checkedInMemberIds = array();
        foreach ($checkins as $checkin) {
            $checkedInMemberIds[] = $checkin['member_id'];
        }

        $event = $event->toArray();
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
                'photo'     => isset($rsvp['member_photo']) ? $rsvp['member_photo'] : null,
                'checkedIn' => in_array($rsvp['member']['member_id'], $checkedInMemberIds)
            );
        }

        $this->saveInCache('event_cache_'.$id, $event);

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
            'event_id'           => $eventId,
            'attendee_member_id' => $userId,
        );

        $response = $this->client->postCheckin($params);

        if ($response->getStatusCode() != 201) {
            return false;
        }

        return true;
    }

    /**
     * @return \DMS\Service\Meetup\AbstractMeetupClient
     */
    public function getClient()
    {
        return $this->client;
    }

    protected function saveInCache($key, $data)
    {
        $value = serialize($data);
        $this->cache->set($key, $value);
    }

    protected function getFromCache($key)
    {
        if (! $this->cache->exists($key)) {
            return null;
        }

        return unserialize($this->cache->get($key));
    }
}
