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

    public function testAddDockedDroneStoresDrone(): void
    {
        $hangar = new Hangar(2);
        $drone = new Drone('DR-101');

        $hangar->addDrone($drone);

        $this->assertSame(1, $hangar->dockedCount());
        $this->assertSame(['DR-101'], $hangar->dockedDroneIds());
        $this->assertTrue($hangar->hasFreeSlot());
    }

    public function testAddMaintenanceDroneStoresDrone(): void
    {
        $hangar = new Hangar(2);
        $drone = new Drone('DR-102', 0, Drone::STATUS_MAINTENANCE);

        $hangar->addDrone($drone);

        $this->assertSame(1, $hangar->maintenanceCount());
        $this->assertSame(['DR-102'], $hangar->maintenanceDroneIds());
        $this->assertTrue($hangar->hasFreeSlot());
    }

    public function testCannotAddRetiredDrone(): void
    {
        $hangar = new Hangar(2);
        $drone = new Drone('DR-103', 0, Drone::STATUS_RETIRED);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot add drone DR-103 with status retired');

        $hangar->addDrone($drone);
    }

    public function testCannotAddInFlightDrone(): void
    {
        $hangar = new Hangar(2);
        $drone = new Drone('DR-104', 0, Drone::STATUS_IN_FLIGHT);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot add drone DR-104 with status in_flight');

        $hangar->addDrone($drone);
    }

    public function testCannotAddDuplicateDroneById(): void
    {
        $hangar = new Hangar(2);
        $hangar->addDrone(new Drone('DR-105'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Drone DR-105 is already known by this hangar');

        $hangar->addDrone(new Drone('DR-105'));
    }

    public function testCannotAddWhenNoFreeSlots(): void
    {
        $hangar = new Hangar(1);
        $hangar->addDrone(new Drone('DR-106'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No free slots available');

        $hangar->addDrone(new Drone('DR-107', 0, Drone::STATUS_MAINTENANCE));
    }


}