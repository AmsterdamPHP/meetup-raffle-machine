<?php

namespace Raffle;

class RandomService
{
    /**
     * Base URL
     */
    const BASE_URL = 'http://www.random.org/integers/?min=%d&max=%d&num=%d&col=1&base=10&format=plain&rnd=new';

    /**
     * Retrieve a block of random numbers.
     *
     * @param int $min   Minimum amount.
     * @param int $max   Maximum amount.
     * @param int $count How many numbers.
     * @return array
     */
    public function getRandomNumbers($min, $max, $count)
    {
        // Construct the URL
        $url = sprintf(self::BASE_URL, $min, $max, $count);

        // Fetch the numbers
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);

        // Decode data and return
        return explode("\n", trim($data));
    }
}