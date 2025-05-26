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

namespace Tests\Sylius\Component\Core\Shipping\Calculator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;

final class FlatRateCalculatorTest extends TestCase
{
    private MockObject&ShipmentInterface $shipment;

    private MockObject&OrderInterface $order;

    private ChannelInterface&MockObject $channel;

    private FlatRateCalculator $calculator;

    protected function setUp(): void
    {
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->calculator = new FlatRateCalculator();
    }

    public function testShouldImplementShippingCalculatorInterface(): void
    {
        $this->assertInstanceOf(CalculatorInterface::class, $this->calculator);
    }

    public function testShouldReturnFlatRateType(): void
    {
        $this->assertSame('flat_rate', $this->calculator->getType());
    }

    public function testShouldCalculateTheFlatRateAmountConfiguredOnTheMethod(): void
    {
        $this->shipment->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB');

        $this->assertSame(1500, $this->calculator->calculate($this->shipment, ['WEB' => ['amount' => 1500]]));
    }

    public function testShouldThrowChannelNotDefinedExceptionIfChannelCodeKeyDoesNotExist(): void
    {
        $this->expectException(MissingChannelConfigurationException::class);
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shipment->expects($this->exactly(2))->method('getOrder')->willReturn($this->order);
        $this->shipment->expects($this->once())->method('getMethod')->willReturn($shippingMethod);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB');
        $this->channel->expects($this->once())->method('getName')->willReturn('WEB');
        $shippingMethod->expects($this->once())->method('getName')->willReturn('UPS');

        $this->calculator->calculate($this->shipment, ['amount' => 200]);
    }
}
