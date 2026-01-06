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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

final class ShipmentTest extends TestCase
{
    private MockObject&ShippingMethodInterface $shippingMethod;

    private MockObject&ShipmentUnitInterface $shipmentUnit;

    private Shipment $shipment;

    protected function setUp(): void
    {
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shipmentUnit = $this->createMock(ShipmentUnitInterface::class);
        $this->shipment = new Shipment();
    }

    public function testShouldImplementShipmentInterface(): void
    {
        $this->assertInstanceOf(ShipmentInterface::class, $this->shipment);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->shipment->getId());
    }

    public function testShouldHaveCartStateByDefault(): void
    {
        $this->assertSame(Shipment::STATE_CART, $this->shipment->getState());
    }

    public function testShouldStateBeMutable(): void
    {
        $this->shipment->setState(Shipment::STATE_SHIPPED);

        $this->assertSame(Shipment::STATE_SHIPPED, $this->shipment->getState());
    }

    public function testShouldNotHaveShippingMethodByDefault(): void
    {
        $this->assertNull($this->shipment->getMethod());
    }

    public function testShouldShippingMethodBeMutable(): void
    {
        $this->shipment->setMethod($this->shippingMethod);

        $this->assertSame($this->shippingMethod, $this->shipment->getMethod());
    }

    public function testShouldInitializeUnitsCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->shipment->getUnits());
    }

    public function testShouldNotHaveAnyUnitByDefault(): void
    {
        $this->assertTrue($this->shipment->getUnits()->isEmpty());
    }

    public function testShouldBeAbleToAddUnits(): void
    {
        $this->shipmentUnit->expects($this->once())->method('setShipment')->with($this->shipment);

        $this->shipment->addUnit($this->shipmentUnit);

        $this->assertTrue($this->shipment->hasUnit($this->shipmentUnit));
    }

    public function testShouldBeAbleToRemoveUnit(): void
    {
        $this->shipment->addUnit($this->shipmentUnit);
        $this->shipmentUnit->expects($this->once())->method('setShipment')->with(null);

        $this->shipment->removeUnit($this->shipmentUnit);

        $this->assertFalse($this->shipment->hasUnit($this->shipmentUnit));
    }

    public function testShouldNotHaveTrackingCodeByDefault(): void
    {
        $this->assertNull($this->shipment->getTracking());
    }

    public function testShouldTrackingCodeBeMutable(): void
    {
        $this->shipment->setTracking('5346172074');

        $this->assertSame('5346172074', $this->shipment->getTracking());
    }

    public function testShouldNotBeTrackedByDefault(): void
    {
        $this->assertFalse($this->shipment->isTracked());
    }

    public function testShouldNotBeTrackedIfTrackingCodeIsNotDefined(): void
    {
        $this->shipment->setTracking(null);

        $this->assertFalse($this->shipment->isTracked());
    }

    public function testShouldBeTrackedOnlyIfTrackingCodeIsDefined(): void
    {
        $this->shipment->setTracking('5346172074');

        $this->assertTrue($this->shipment->isTracked());
    }

    public function testShouldInitilizeCreationDateByDefault(): void
    {
        $this->assertInstanceOf(\DateTime::class, $this->shipment->getCreatedAt());
    }

    public function testShouldCreationDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shipment->setCreatedAt($date);

        $this->assertSame($date, $this->shipment->getCreatedAt());
    }

    public function testShouldNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->shipment->getUpdatedAt());
    }

    public function testShouldLastUpdateDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shipment->setUpdatedAt($date);

        $this->assertSame($date, $this->shipment->getUpdatedAt());
    }
}
