<?php

namespace Raffle;

use RandomLib\Factory;
use SecurityLib\Strength;

class RandomService
{
    /**
     * Base URL
     */
    const BASE_URL = 'https://www.random.org/integer-sets/?sets=1&min=%d&max=%d&num=%d&order=random&format=plain&rnd=new';

    /**
     * Retrieve a block of random numbers.
     *
     * @param int $min   Minimum amount.
     * @param int $max   Maximum amount.
     * @return array
     */
    public function getRandomNumbers($min, $max)
    {
        $factory = new Factory();
        $generator = $factory->getGenerator(new Strength(Strength::MEDIUM));

        $numbers = [];

        while(count($numbers) < ($max + 1)) {
            $numbers[] = $generator->generateInt($min, $max);
            $numbers   = array_unique($numbers);
        }

        // Decode data and return
        return array_values($numbers);
    }
}
