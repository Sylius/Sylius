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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Core\Resolver\EligibleDefaultShippingMethodResolver;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

final class EligibleDefaultShippingMethodResolverTest extends TestCase
{
    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private MockObject&ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker;

    private MockObject&ZoneMatcherInterface $zoneMatcher;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private MockObject&ShipmentInterface $shipment;

    private MockObject&ShippingMethodInterface $firstShippingMethod;

    private MockObject&ShippingMethodInterface $secondShippingMethod;

    private EligibleDefaultShippingMethodResolver $resolver;

    protected function setUp(): void
    {
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->shippingMethodEligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->zoneMatcher = $this->createMock(ZoneMatcherInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->firstShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->secondShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->resolver = new EligibleDefaultShippingMethodResolver(
            $this->shippingMethodRepository,
            $this->shippingMethodEligibilityChecker,
            $this->zoneMatcher,
        );
    }

    public function testShouldImplementDefaultShippingMethodResolverInterface(): void
    {
        $this->assertInstanceOf(DefaultShippingMethodResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnFirstEnabledAndEligibleShippingMethodFromShipmentOrderChannelAsDefault(): void
    {
        $this->shipment->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn(null);
        $this->shippingMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($this->channel)
            ->willReturn([$this->firstShippingMethod, $this->secondShippingMethod]);
        $this->shippingMethodEligibilityChecker
            ->expects($this->exactly(2))
            ->method('isEligible')
            ->willReturnMap([
                [$this->shipment, $this->firstShippingMethod, false],
                [$this->shipment, $this->secondShippingMethod, true],
            ]);

        $this->assertEquals(
            $this->secondShippingMethod,
            $this->resolver->getDefaultShippingMethod($this->shipment),
        );
    }

    public function testShouldReturnEnabledAndEligibleShippingMethodFromShipmentOrderChannelAndShippingZoneAsDefault(): void
    {
        $shippingAddress = $this->createMock(AddressInterface::class);
        $zone = $this->createMock(ZoneInterface::class);
        $this->shipment->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn($shippingAddress);
        $this->zoneMatcher->expects($this->once())->method('matchAll')->with($shippingAddress)->willReturn([$zone]);
        $this->shippingMethodRepository
            ->expects($this->once())
            ->method('findEnabledForZonesAndChannel')
            ->with([$zone], $this->channel)
            ->willReturn([$this->firstShippingMethod, $this->secondShippingMethod]);

        $this->shippingMethodEligibilityChecker
            ->expects($this->exactly(2))
            ->method('isEligible')
            ->willReturnMap([
                [$this->shipment, $this->firstShippingMethod, false],
                [$this->shipment, $this->secondShippingMethod, true],
            ]);

        $this->assertEquals(
            $this->secondShippingMethod,
            $this->resolver->getDefaultShippingMethod($this->shipment),
        );
    }

    public function testShouldThrowExceptionIfShippingMethodCannotBeResolved(): void
    {
        $this->expectException(UnresolvedDefaultShippingMethodException::class);
        $this->shipment->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getShippingAddress')->willReturn(null);
        $this->shippingMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($this->channel)
            ->willReturn([$this->firstShippingMethod, $this->secondShippingMethod]);
        $this->shippingMethodEligibilityChecker
            ->expects($this->exactly(2))
            ->method('isEligible')
            ->willReturnMap([
                [$this->shipment, $this->firstShippingMethod, false],
                [$this->shipment, $this->secondShippingMethod, false],
            ]);

        $this->resolver->getDefaultShippingMethod($this->shipment);
    }

    public function testShouldThrowExceptionIfPassedShipmentIsNotCoreShipmentObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->resolver->getDefaultShippingMethod($this->createMock(BaseShipmentInterface::class));
    }
}
