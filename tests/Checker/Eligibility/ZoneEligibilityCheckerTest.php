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

namespace Sylius\Tests\Checker\Eligibility;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Shipping\Checker\Eligibility\ZoneEligibilityChecker;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ZoneEligibilityCheckerTest extends TestCase
{
    private ZoneMatcherInterface $zoneMatcher;
    private ZoneEligibilityChecker $checker;
    private ShipmentInterface $shipment;
    private OrderInterface $order;
    private AddressInterface $address;
    private ShippingMethodInterface $shippingMethod;
    private ZoneInterface $zone1;
    private ZoneInterface $zone2;
    private ZoneInterface $shippingMethodZone;

    protected function setUp(): void
    {
        $this->zoneMatcher = $this->createMock(ZoneMatcherInterface::class);
        $this->checker = new ZoneEligibilityChecker($this->zoneMatcher);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);

        $this->zone1 = $this->createMock(ZoneInterface::class);
        $this->zone2 = $this->createMock(ZoneInterface::class);
        $this->shippingMethodZone = $this->createMock(ZoneInterface::class);
    }

    public function test_it_implements_interface(): void
    {
        $this->assertInstanceOf(
            ShippingMethodEligibilityCheckerInterface::class,
            $this->checker
        );
    }

    public function test_it_returns_true_if_no_shipping_address(): void
    {
        $this->order->method('getShippingAddress')->willReturn(null);
        $this->shipment->method('getOrder')->willReturn($this->order);

        $this->assertTrue($this->checker->isEligible($this->shipment, $this->shippingMethod));
    }

    public function test_it_returns_true_if_zone_matches(): void
    {
        $this->zone1->method('getCode')->willReturn('EU');
        $this->zone2->method('getCode')->willReturn('US');
        $this->shippingMethodZone->method('getCode')->willReturn('US');

        $this->order->method('getShippingAddress')->willReturn($this->address);
        $this->shipment->method('getOrder')->willReturn($this->order);

        $this->zoneMatcher
            ->method('matchAll')
            ->with($this->address, Scope::SHIPPING)
            ->willReturn([$this->zone1, $this->zone2]);

        $this->shippingMethod->method('getZone')->willReturn($this->shippingMethodZone);

        $this->assertTrue($this->checker->isEligible($this->shipment, $this->shippingMethod));
    }

    public function test_it_returns_false_if_no_zone_matches(): void
    {
        $this->zone1->method('getCode')->willReturn('EU');
        $this->zone2->method('getCode')->willReturn('US');
        $this->shippingMethodZone->method('getCode')->willReturn('ASIA');

        $this->order->method('getShippingAddress')->willReturn($this->address);
        $this->shipment->method('getOrder')->willReturn($this->order);

        $this->zoneMatcher
            ->method('matchAll')
            ->with($this->address, Scope::SHIPPING)
            ->willReturn([$this->zone1, $this->zone2]);

        $this->shippingMethod->method('getZone')->willReturn($this->shippingMethodZone);

        $this->assertFalse($this->checker->isEligible($this->shipment, $this->shippingMethod));
    }
}
