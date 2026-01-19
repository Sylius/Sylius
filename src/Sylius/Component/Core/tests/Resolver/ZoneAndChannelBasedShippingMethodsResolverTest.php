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

namespace Tests\Sylius\Component\Core\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class ZoneAndChannelBasedShippingMethodsResolverTest extends TestCase
{
    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private MockObject&ZoneMatcherInterface $zoneMatcher;

    private MockObject&ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private MockObject&OrderInterface $order;

    private AddressInterface&MockObject $address;

    private ChannelInterface&MockObject $channel;

    private MockObject&ShipmentInterface $shipment;

    private MockObject&ZoneInterface $firstZone;

    private MockObject&ZoneInterface $secondZone;

    private MockObject&ShippingMethodInterface $firstShippingMethod;

    private MockObject&ShippingMethodInterface $secondShippingMethod;

    private ZoneAndChannelBasedShippingMethodsResolver $resolver;

    protected function setUp(): void
    {
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->zoneMatcher = $this->createMock(ZoneMatcherInterface::class);
        $this->eligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->firstZone = $this->createMock(ZoneInterface::class);
        $this->secondZone = $this->createMock(ZoneInterface::class);
        $this->firstShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->secondShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->resolver = new ZoneAndChannelBasedShippingMethodsResolver(
            $this->shippingMethodRepository,
            $this->zoneMatcher,
            $this->eligibilityChecker,
        );
    }

    public function testShouldImplementShippingMethodsByZonesAndChannelResolverInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodsResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnShippingMethodsMatchedForShipmentOrderShippingAddressAndOrderChannel(): void
    {
        $this->shipment->expects($this->exactly(4))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->exactly(2))->method('getShippingAddress')->willReturn($this->address);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->zoneMatcher
            ->expects($this->once())
            ->method('matchAll')
            ->with($this->address, Scope::SHIPPING)
            ->willReturn([$this->firstZone, $this->secondZone]);
        $this->shippingMethodRepository
            ->expects($this->once())
            ->method('findEnabledForZonesAndChannel')
            ->with([$this->firstZone, $this->secondZone], $this->channel)
            ->willReturn([$this->firstShippingMethod, $this->secondShippingMethod]);
        $this->eligibilityChecker
            ->expects($this->exactly(2))
            ->method('isEligible')
            ->willReturnMap([
                [$this->shipment, $this->firstShippingMethod, true],
                [$this->shipment, $this->secondShippingMethod, true],
            ]);

        $this->assertEquals(
            [$this->firstShippingMethod, $this->secondShippingMethod],
            $this->resolver->getSupportedMethods($this->shipment),
        );
    }

    public function testShouldReturnEmptyArrayIfZoneMatcherCouldNotMatchAnyZone(): void
    {
        $this->shipment->expects($this->exactly(4))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->exactly(2))->method('getShippingAddress')->willReturn($this->address);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->zoneMatcher
            ->expects($this->once())
            ->method('matchAll')
            ->with($this->address, Scope::SHIPPING)
            ->willReturn([]);

        $this->assertEquals([], $this->resolver->getSupportedMethods($this->shipment));
    }

    public function testShouldReturnOnlyShippingMethodsThatAreEligible(): void
    {
        $this->shipment->expects($this->exactly(4))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->exactly(2))->method('getShippingAddress')->willReturn($this->address);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->zoneMatcher
            ->expects($this->once())
            ->method('matchAll')
            ->with($this->address, Scope::SHIPPING)
            ->willReturn([$this->firstZone, $this->secondZone]);
        $this->eligibilityChecker
            ->expects($this->exactly(2))
            ->method('isEligible')
            ->willReturnMap([
                [$this->shipment, $this->firstShippingMethod, false],
                [$this->shipment, $this->secondShippingMethod, true],
            ]);
        $this->shippingMethodRepository
            ->expects($this->once())
            ->method('findEnabledForZonesAndChannel')
            ->with([$this->firstZone, $this->secondZone], $this->channel)
            ->willReturn([$this->firstShippingMethod, $this->secondShippingMethod]);

        $this->assertEquals(
            [$this->secondShippingMethod],
            $this->resolver->getSupportedMethods($this->shipment),
        );
    }

    public function testShouldSupportShipmentsWithOrderAndItsShippingAddressDefined(): void
    {
        $this->shipment->expects($this->exactly(3))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn($this->address);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);

        $this->assertTrue($this->resolver->supports($this->shipment));
    }

    public function testShouldNotSupportShipmentsWhichOrderHasNoShippingAddressDefined(): void
    {
        $this->shipment->expects($this->exactly(2))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn(null);

        $this->assertFalse($this->resolver->supports($this->shipment));
    }

    public function testShouldNotSupportShipmentsForOrderWithNotAssignedChannel(): void
    {
        $this->shipment->expects($this->exactly(3))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn($this->address);
        $this->order->expects($this->once())->method('getChannel')->willReturn(null);

        $this->assertFalse($this->resolver->supports($this->shipment));
    }

    public function testShouldNotSupportShipmentsWhichHasNoOrderDefined(): void
    {
        $this->shipment->expects($this->once())->method('getOrder')->willReturn(null);

        $this->assertFalse($this->resolver->supports($this->shipment));
    }

    public function testShouldNotSupportDifferentShippingSubjectThanShipment(): void
    {
        $this->assertFalse(
            $this->resolver->supports($this->createMock(ShippingSubjectInterface::class)),
        );
    }
}
