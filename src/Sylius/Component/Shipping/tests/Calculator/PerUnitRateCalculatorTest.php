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
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Calculator\PerUnitRateCalculator;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class PerUnitRateCalculatorTest extends TestCase
{
    private MockObject&ShipmentInterface $shipment;

    private PerUnitRateCalculator $calculator;

    protected function setUp(): void
    {
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->calculator = new PerUnitRateCalculator();
    }

    public function testShouldImplementShippingCalculatorInterface(): void
    {
        $this->assertInstanceOf(CalculatorInterface::class, $this->calculator);
    }

    public function testShouldReturnPerUnitType(): void
    {
        $this->assertSame('per_unit_rate', $this->calculator->getType());
    }

    public function testShouldCalculateTheTotalWithThePerUnitAmountConfiguredOnTheMethod(): void
    {
        $this->shipment->expects($this->once())->method('getShippingUnitCount')->willReturn(11);

        $this->assertSame(2200, $this->calculator->calculate($this->shipment, ['amount' => 200]));
    }
}
