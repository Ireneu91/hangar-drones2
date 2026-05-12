<?php

declare(strict_types=1);

namespace Tests;

use App\Drone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DroneTest extends TestCase
{
    public function testConstructsWithDefaultValues(): void
    {
        $drone = new Drone('DR-001');

        $this->assertSame('DR-001', $drone->id());
        $this->assertSame(0, $drone->flightMinutes());
        $this->assertSame(Drone::STATUS_DOCKED, $drone->status());
        $this->assertTrue($drone->isDocked());
        $this->assertFalse($drone->isInFlight());
        $this->assertFalse($drone->isInMaintenance());
        $this->assertFalse($drone->isRetired());
    }

    public function testConstructsWithCustomStatusAndMinutes(): void
    {
        $drone = new Drone('DR-002', 120, Drone::STATUS_MAINTENANCE);

        $this->assertSame('DR-002', $drone->id());
        $this->assertSame(120, $drone->flightMinutes());
        $this->assertSame(Drone::STATUS_MAINTENANCE, $drone->status());
        $this->assertFalse($drone->isDocked());
        $this->assertFalse($drone->isInFlight());
        $this->assertTrue($drone->isInMaintenance());
    }


}
