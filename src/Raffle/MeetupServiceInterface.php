<?php
declare(strict_types=1);

namespace App\Raffle;

interface MeetupServiceInterface
{
    public function getPresentAndPastEvents(): array;
    public function getEvent(string $id): array;
}
