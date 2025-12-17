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

namespace Tests\Sylius\Component\Core\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class OrderShippingMethodSelectionRequirementCheckerTest extends TestCase
{
    private MockObject&ShippingMethodsResolverInterface $shippingMethodsResolver;

    private MockObject&OrderInterface $order;

    private ChannelInterface&MockObject $channel;

    private MockObject&ShipmentInterface $shipment;

    private MockObject&ShippingMethodInterface $shippingMethod;

    private OrderShippingMethodSelectionRequirementChecker $checker;

    protected function setUp(): void
    {
        $this->shippingMethodsResolver = $this->createMock(ShippingMethodsResolverInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->checker = new OrderShippingMethodSelectionRequirementChecker($this->shippingMethodsResolver);
    }

    public function testShouldImplementOrderShippingNecessityCheckerInterface(): void
    {
        $this->assertInstanceOf(OrderShippingMethodSelectionRequirementCheckerInterface::class, $this->checker);
    }

    public function testShouldSayThatShippingMethodDoNotHaveToBeSelectedIfNoneOfVariantsFromOrderRequiresShipping(): void
    {
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(false);

        $this->assertFalse($this->checker->isShippingMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatShippingMethodDoNotHaveToBeSelectedIfOrderVariantsRequireShippingButThereIsOnlyOneShippingMethodAvailable(): void
    {
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('isSkippingShippingStepAllowed')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->shipment]));
        $this->shippingMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->shipment)
            ->willReturn([$this->shippingMethod]);

        $this->assertFalse($this->checker->isShippingMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatShippingMethodHaveToBeSelectedIfOrderVariantsRequireShippingAndOrderHasNoShipmentYet(): void
    {
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(false);

        $this->assertTrue($this->checker->isShippingMethodSelectionRequired($this->order));
    }

    public function testShouldSaysThatShippingMethodHaveToBeSelectedIfOrderVariantsRequireShippingAdnChannelDoesNotAllowToSkipShippingStep(): void
    {
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('isSkippingShippingStepAllowed')->willReturn(false);

        $this->assertTrue($this->checker->isShippingMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatShippingMethodHaveToBeSelectedIfOrderVariantsRequireShippingAndThereIsMoreThanOneShippingMethodAvailable(): void
    {
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('isSkippingShippingStepAllowed')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->shipment]));
        $this->shippingMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->shipment)
            ->willReturn([$this->shippingMethod, $this->createMock(ShippingMethodInterface::class)]);

        $this->assertTrue($this->checker->isShippingMethodSelectionRequired($this->order));
    }
}
