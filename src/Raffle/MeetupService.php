<?php

namespace Raffle;

use DMS\Service\Meetup\AbstractMeetupClient;
use DMS\Service\Meetup\Response\MultiResultResponse;
use Predis\Client;

final class MeetupService
{
    /**
     * Meetup client
     *
     * @var AbstractMeetupClient
     */
    private $client;

    /**
     * Meetup group
     *
     * @var string
     */
    private $group;

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
     * @param bool $bustCache
     *
     * @return MultiResultResponse
     */
    public function getEvents($bustCache = false)
    {
        $cached = $this->getFromCache('events_cache');
        if ($bustCache == false && $cached !== null) {
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

        $this->saveInCache('events_cache', $events);

        return $events;
    }

    /**
     * @param bool $bustCache
     *
     * @return mixed
     */
    public function getPresentAndPastEvents($bustCache = false)
    {
        $events = $this->getEvents($bustCache);

        // Filter out events further in the future than a day
        $dayFromNow = (time() + (24 * 60 * 60)) * 1000;
        return $events->filter(function($value) use ($dayFromNow) {
            return ($value['time'] < $dayFromNow);
        });
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
        $event = $this->client->getEvent(array('id' => $id));

        $rsvps = $this->client->getRSVPs(
            array('event_id' => $id, 'rsvp' => 'yes', 'order' => 'name', 'fields' => 'host', 'page' => 120)
        );

        $event = $event->toArray();
        $event['checkins'] = array();
        $event['rsvps']    = array();
        foreach ($rsvps as $rsvp) {
            $event['rsvps'][] = array(
                'id'        => $rsvp['member']['member_id'],
                'name'      => $rsvp['member']['name'],
                'photo'     => isset($rsvp['member_photo']) ? $rsvp['member_photo'] : null,
                'host'      => $rsvp['host']
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
        $this->cache->expire($key, 3600);
    }

    protected function getFromCache($key)
    {
        if (! $this->cache->exists($key)) {
            return null;
        }

        return unserialize($this->cache->get($key));
    }
}
