<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Shipping\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnit;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

final class ShipmentUnitTest extends TestCase
{
    private MockObject&ShipmentInterface $shipment;

    private MockObject&ShippableInterface $shippable;

    private ShipmentUnit $shipmentUnit;

    protected function setUp(): void
    {
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->shippable = $this->createMock(ShippableInterface::class);
        $this->shipmentUnit = new ShipmentUnit();
    }

    public function testShouldImplementShipmentUnitInterface(): void
    {
        $this->assertInstanceOf(ShipmentUnitInterface::class, $this->shipmentUnit);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->shipmentUnit->getId());
    }

    public function testShouldNotBelongToShipmentByDefault(): void
    {
        $this->assertNull($this->shipmentUnit->getShipment());
    }

    public function testShouldAllowAssignItselfToShipment(): void
    {
        $this->shipmentUnit->setShipment($this->shipment);

        $this->assertSame($this->shipment, $this->shipmentUnit->getShipment());
    }

    public function testShouldAllowDetachItselfFromShipment(): void
    {
        $this->shipmentUnit->setShipment($this->shipment);

        $this->shipmentUnit->setShipment(null);

        $this->assertNull($this->shipmentUnit->getShipment());
    }

    public function testShouldHaveNoShippableDefinedByDefault(): void
    {
        $this->assertNull($this->shipmentUnit->getShippable());
    }

    public function testShouldAllowDefiningShippable(): void
    {
        $this->shipmentUnit->setShippable($this->shippable);

        $this->assertSame($this->shippable, $this->shipmentUnit->getShippable());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->shipmentUnit->getCreatedAt());
    }

    public function testShouldCreationDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shipmentUnit->setCreatedAt($date);

        $this->assertSame($date, $this->shipmentUnit->getCreatedAt());
    }

    public function testShouldNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->shipmentUnit->getUpdatedAt());
    }

    public function testShouldLastUpdateDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shipmentUnit->setUpdatedAt($date);

        $this->assertSame($date, $this->shipmentUnit->getUpdatedAt());
    }
}
