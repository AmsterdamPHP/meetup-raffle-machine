<?php
declare(strict_types=1);

namespace App\Raffle;

use RandomLib\Factory;
use SecurityLib\Strength;

final class RandomService
{
    /**
     * Retrieve a block of random numbers.
     */
    public function getRandomNumbers(int $amount): array
    {
        $factory = new Factory();
        $generator = $factory->getGenerator(new Strength(Strength::MEDIUM));

        $numbers = [];

        while (count($numbers) < $amount) {
            // Amount minus 1 since we want results ranging from zero till amount
            $numbers[] = $generator->generateInt(0, $amount - 1);
            $numbers   = array_unique($numbers);
        }

        // Decode data and return
        return array_values($numbers);
    }
}
