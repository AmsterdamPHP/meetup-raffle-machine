<?php
declare(strict_types=1);

namespace App\Raffle;

use Predis\Client;
use Predis\ClientInterface;

final class CheckInService
{
    /**
     * @var Client
     */
    private $predis;

    public function __construct(ClientInterface $predis)
    {
        $this->predis = $predis;
    }

    public function checkIn(string $eventId, string $userId): void
    {
        $this->predis->lpush('checkin_'.$eventId, [$userId]);
    }

    public function getCheckInsForEvent(string $eventId): array
    {
        return array_filter($this->predis->lrange('checkin_'.$eventId, 0, 300));
    }
}
