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
use Sylius\Component\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class FlatRateCalculatorTest extends TestCase
{
    private MockObject&ShipmentInterface $shipment;

    private FlatRateCalculator $calculator;

    protected function setUp(): void
    {
        $this->shipment = $this->createMock(ShipmentInterface::class);
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
        $this->assertSame(1500, $this->calculator->calculate($this->shipment, ['amount' => 1500]));
    }
}
