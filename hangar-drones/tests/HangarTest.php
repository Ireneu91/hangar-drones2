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

    
    public function testLaunchDroneFromDocked(): void
    {
        $hangar = new Hangar(3);
        $hangar->addDrone(new Drone('DR-108'));
        $hangar->addDrone(new Drone('DR-109'));

        $launched = $hangar->launchDrone();

        $this->assertSame('DR-108', $launched->id());
        $this->assertSame(Drone::STATUS_IN_FLIGHT, $launched->status());
        $this->assertSame(1, $hangar->inFlightCount());
        $this->assertSame(['DR-109'], $hangar->dockedDroneIds());
    }

    public function testLaunchDroneThrowsWhenNoDocked(): void
    {
        $hangar = new Hangar(2);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No drones docked');

        $hangar->launchDrone();
    }

    public function testLandDroneAddsFlightMinutesAndMovesToMaintenance(): void
    {
        $hangar = new Hangar(3);
        $drone = new Drone('DR-110');
        $hangar->addDrone($drone);
        $launched = $hangar->launchDrone();

        $hangar->landDrone($launched, 25);

        $this->assertSame(25, $launched->flightMinutes());
        $this->assertSame(Drone::STATUS_MAINTENANCE, $launched->status());
        $this->assertSame(0, $hangar->inFlightCount());
        $this->assertSame(1, $hangar->maintenanceCount());
        $this->assertSame(['DR-110'], $hangar->maintenanceDroneIds());
    }

    public function testLandDroneThrowsForNegativeFlightMinutes(): void
    {
        $hangar = new Hangar(2);
        $drone = new Drone('DR-111');
        $hangar->addDrone($drone);
        $launched = $hangar->launchDrone();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('flightMinutes must be >= 0');

        $hangar->landDrone($launched, -1);
    }

    public function testLandDroneThrowsWhenDroneNotInFlightFromThisHangar(): void
    {
        $hangar = new Hangar(2);
        $drone = new Drone('DR-112');
        $drone->takeOff();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Drone DR-112 is not in flight from this hangar');

        $hangar->landDrone($drone, 10);
    }



}