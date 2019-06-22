<?php
declare(strict_types=1);

namespace App\Raffle;

use DMS\Service\Meetup\AbstractMeetupClient;
use DMS\Service\Meetup\Response\MultiResultResponse;
use Predis\ClientInterface;

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
     * @var ClientInterface
     */
    private $cache;

    public function __construct(AbstractMeetupClient $client, string $group, ClientInterface $cache)
    {
        $this->client = $client;
        $this->group  = $group;
        $this->cache = $cache;
    }

    /**
     * Fetch all events in the past and up to a day in the future.
     */
    private function getEvents(bool $bustCache = false): MultiResultResponse
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

    public function getPresentAndPastEvents(bool $bustCache = false): array
    {
        $events = $this->getEvents($bustCache);

        // Filter out events further in the future than a day
        $dayFromNow = (time() + (24 * 60 * 60)) * 1000;
        return $events->filter(function($value) use ($dayFromNow) {
            return ($value['time'] < $dayFromNow);
        })->toArray();
    }

    /**
     * Get a single event.
     */
    public function getEvent(string $id): array
    {
        $cached = $this->getFromCache('event_cache_'.$id);
        if ($cached !== null) {
            return $cached;
        }

        // Fetch, event, checkins and RSVPs (only the latter has pictures)
        $event = $this->client->getEvents(['event_id' => $id]);

        $rsvps = $this->client->getRsvps(
            ['event_id' => $id, 'rsvp' => 'yes', 'order' => 'name', 'fields' => 'host', 'page' => 300]
        );

        $event = $event->toArray()[0];
        $event['checkins'] = [];
        $event['rsvps']    = [];
        foreach ($rsvps as $rsvp) {
            $event['rsvps'][] = array(
                'id'    => $rsvp['member']['member_id'],
                'name'  => $rsvp['member']['name'],
                'photo' => $rsvp['member_photo'] ?? null,
                'host'  => $rsvp['host']
            );
        }

        $this->saveInCache('event_cache_'.$id, $event);

        return $event;
    }

    private function saveInCache(string $key, $data): void
    {
        $value = serialize($data);
        $this->cache->set($key, $value);
        $this->cache->expire($key, 3600);
    }

    private function getFromCache(string $key)
    {
        if (! $this->cache->exists($key)) {
            return null;
        }

        return unserialize($this->cache->get($key));
    }
}
