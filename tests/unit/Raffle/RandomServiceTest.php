<?php

namespace App\Tests\Raffle;

use App\Raffle\RandomService;
use PHPUnit\Framework\TestCase;

final class RandomServiceTest extends TestCase
{
    /**
     * @var RandomService
     */
    private $service;

    public function setUp(): void
    {
        $this->service = new RandomService();
    }

    public function test_amount_of_random_numbers_returned(): void
    {
        $numbers = $this->service->getRandomNumbers(10);
        $this->assertCount(10, $numbers);
    }

    public function test_list_of_random_numbers_are_unique(): void
    {
        $numbers = $this->service->getRandomNumbers(10);
        $uniqueNumbers = array_unique($numbers);
        $this->assertSame($numbers, $uniqueNumbers);
    }

    public function test_list_is_empty_when_amount_zero(): void
    {
        // The service should not return any numbers if the amount is zero
        $this->assertEmpty($this->service->getRandomNumbers(0));
    }

    public function test_list_includes_zero(): void
    {
        // The service should return _all_ numbers for $amount in random order
        $this->assertContains(0, $this->service->getRandomNumbers(1));
        $this->assertContains(0, $this->service->getRandomNumbers(2));
    }
}
