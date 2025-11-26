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

namespace Tests\Sylius\Component\Shipping\Calculator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculator;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

final class DelegatingCalculatorTest extends TestCase
{
    private MockObject&ServiceRegistryInterface $serviceRegistry;

    private MockObject&ShipmentInterface $shipment;

    private DelegatingCalculator $delegatingCalculator;

    protected function setUp(): void
    {
        $this->serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->delegatingCalculator = new DelegatingCalculator($this->serviceRegistry);
    }

    public function testShouldImplementDelegatingShippingCalculatorInterface(): void
    {
        $this->assertInstanceOf(DelegatingCalculatorInterface::class, $this->delegatingCalculator);
    }

    public function testShouldComplainIfShipmentHasNoMethodDefined(): void
    {
        $this->expectException(UndefinedShippingMethodException::class);
        $this->shipment->expects($this->once())->method('getMethod')->willReturn(null);

        $this->delegatingCalculator->calculate($this->shipment);
    }

    public function testShouldDelegateCalculationToCalculatorDefinedOnShippingMethod(): void
    {
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $calculator = $this->createMock(CalculatorInterface::class);

        $this->shipment->expects($this->once())->method('getMethod')->willReturn($shippingMethod);
        $shippingMethod->expects($this->once())->method('getCalculator')->willReturn('default');
        $shippingMethod->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->serviceRegistry->expects($this->once())->method('get')->with('default')->willReturn($calculator);
        $calculator->expects($this->once())->method('calculate')->with($this->shipment, [])->willReturn(1000);

        $this->assertSame(1000, $this->delegatingCalculator->calculate($this->shipment));
    }
}
