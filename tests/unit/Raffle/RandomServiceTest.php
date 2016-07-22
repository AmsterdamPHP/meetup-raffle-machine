<?php

namespace Raffle;

final class RandomServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RandomService
     */
    private $service;

    public function setUp()
    {
        $this->service = new RandomService();
    }

    public function test_amount_of_random_numbers_returned()
    {
        $numbers = $this->service->getRandomNumbers(10);
        $this->assertCount(10, $numbers);
    }

    public function test_list_of_random_numbers_are_unique()
    {
        $numbers = $this->service->getRandomNumbers(10);
        $uniqueNumbers = array_unique($numbers);
        $this->assertSame($numbers, $uniqueNumbers);
    }
}
