<?php
declare(strict_types=1);

namespace App\Raffle;

use DMS\Service\Meetup\AbstractMeetupClient;

final class MeetupService implements MeetupServiceInterface
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

    public function __construct(AbstractMeetupClient $client, string $group)
    {
        $this->client = $client;
        $this->group  = $group;
    }

    public function getPresentAndPastEvents(): array
    {
        $events = $this->client->getEvents(
            [
                'group_urlname' => $this->group,
                'status' => 'past,upcoming',
                'desc' => 'desc'
            ]
        );

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
        // Fetch, event, checkins and RSVPs (only the latter has pictures)
        $event = $this->client->getEvents(['event_id' => $id]);

        $rsvps = $this->client->getRsvps(
            ['event_id' => $id, 'rsvp' => 'yes', 'order' => 'name', 'fields' => 'host', 'page' => 300]
        );

        $event = $event->toArray()[0];
        $event['checkins'] = [];
        $event['rsvps'] = [];
        foreach ($rsvps as $rsvp) {
            $event['rsvps'][] = array(
                'id' => $rsvp['member']['member_id'],
                'name' => $rsvp['member']['name'],
                'photo' => $rsvp['member_photo'] ?? null,
                'host' => $rsvp['host']
            );
        }

        return $event;
    }
}
