<?php

declare(strict_types=1);

namespace Tests;

use App\Drone;
use App\Hangar;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HangarTest extends TestCase
{
    public function testConstructsWithValidCapacity(): void
    {
        $hangar = new Hangar(2);

        $this->assertSame(2, $hangar->capacity());
        $this->assertTrue($hangar->hasFreeSlot());
        $this->assertSame(0, $hangar->insideCount());
    }

    public function testConstructThrowsOnInvalidCapacity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('capacity must be >= 1');

        new Hangar(0);
    }
}