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

    public function testTakeOffFromDockedChangesStatus(): void
    {
        $drone = new Drone('DR-003');

        $drone->takeOff();

        $this->assertSame(Drone::STATUS_IN_FLIGHT, $drone->status());
        $this->assertTrue($drone->isInFlight());
    }

    public function testMarkDockedFromInFlightChangesStatus(): void
    {
        $drone = new Drone('DR-004');
        $drone->takeOff();

        $drone->markDocked();

        $this->assertSame(Drone::STATUS_DOCKED, $drone->status());
        $this->assertTrue($drone->isDocked());
    }

    public function testSendToMaintenanceFromDockedChangesStatus(): void
    {
        $drone = new Drone('DR-005');

        $drone->sendToMaintenance();

        $this->assertSame(Drone::STATUS_MAINTENANCE, $drone->status());
        $this->assertTrue($drone->isInMaintenance());
    }

    
    public function testReturnFromMaintenanceMovesBackToDocked(): void
    {
        $drone = new Drone('DR-006', 0, Drone::STATUS_MAINTENANCE);

        $drone->returnFromMaintenance();

        $this->assertSame(Drone::STATUS_DOCKED, $drone->status());
        $this->assertTrue($drone->isDocked());
    }

    public function testRetireFromMaintenanceChangesStatus(): void
    {
        $drone = new Drone('DR-007', 0, Drone::STATUS_MAINTENANCE);

        $drone->retire();

        $this->assertSame(Drone::STATUS_RETIRED, $drone->status());
        $this->assertTrue($drone->isRetired());
    }

    public function testCannotTakeOffWhenNotDocked(): void
    {
        $drone = new Drone('DR-008', 0, Drone::STATUS_MAINTENANCE);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('cannot take off');

        $drone->takeOff();
    }

    public function testCannotMarkDockedWhenNotInFlight(): void
    {
        $drone = new Drone('DR-009');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('cannot be set to docked');

        $drone->markDocked();
    }

    public function testCannotSendToMaintenanceWhenNotDocked(): void
    {
        $drone = new Drone('DR-010', 0, Drone::STATUS_IN_FLIGHT);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('cannot enter maintenance');

        $drone->sendToMaintenance();
    }

    
    public function testCannotReturnFromMaintenanceWhenNotInMaintenance(): void
    {
        $drone = new Drone('DR-011');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not in maintenance');

        $drone->returnFromMaintenance();
    }

        public function testCannotRetireWhenNotInMaintenance(): void
    {
        $drone = new Drone('DR-012');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('cannot be retired');

        $drone->retire();
    }

    public function testAddFlightMinutesWhileInFlight(): void
    {
        $drone = new Drone('DR-013');
        $drone->takeOff();

        $drone->addFlightMinutes(45);

        $this->assertSame(45, $drone->flightMinutes());
    }

    public function testCannotAddFlightMinutesWhenNotInFlight(): void
    {
        $drone = new Drone('DR-014');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not in flight');

        $drone->addFlightMinutes(10);
    }

    public function testCannotAddNegativeFlightMinutes(): void
    {
        $drone = new Drone('DR-015');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('flightMinutes must be >= 0');

        $drone->addFlightMinutes(-5);
    }


}
