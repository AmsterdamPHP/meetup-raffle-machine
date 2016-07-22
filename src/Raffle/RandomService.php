<?php

namespace Raffle;

use RandomLib\Factory;
use SecurityLib\Strength;

final class RandomService
{
    /**
     * Retrieve a block of random numbers.
     *
     * @param int $amount How many random numbers you require
     * @return array
     */
    public function getRandomNumbers($amount)
    {
        $factory = new Factory();
        $generator = $factory->getGenerator(new Strength(Strength::MEDIUM));

        $numbers = [];

        while(count($numbers) < $amount) {
            // Amount minus 1 since we want results ranging from zero till amount
            $numbers[] = $generator->generateInt(0, $amount - 1);
            $numbers   = array_unique($numbers);
        }

        // Decode data and return
        return array_values($numbers);
    }
}
