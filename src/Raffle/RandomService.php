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
            $numbers[] = $generator->generateInt(0, $amount);
            $numbers   = array_unique($numbers);
        }

        // Decode data and return
        return array_values($numbers);
    }
}
