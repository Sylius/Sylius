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

namespace Tests\Sylius\Component\Taxation\Calculator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Calculator\DelegatingCalculator;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class DelegatingCalculatorTest extends TestCase
{
    /** @var ServiceRegistryInterface&MockObject */
    private MockObject $serviceRegistry;

    /** @var CalculatorInterface&MockObject */
    private MockObject $calculator;

    /** @var TaxRateInterface&MockObject */
    private MockObject $taxRate;

    private DelegatingCalculator $delegatingCalculator;

    protected function setUp(): void
    {
        $this->serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->calculator = $this->createMock(CalculatorInterface::class);
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->delegatingCalculator = new DelegatingCalculator($this->serviceRegistry);
    }

    public function testShouldImplementCalculatorInterface(): void
    {
        $this->assertInstanceOf(CalculatorInterface::class, $this->delegatingCalculator);
    }

    public function testShouldDelegateCalculationToCorrectCalculator(): void
    {
        $this->taxRate->expects($this->once())->method('getCalculator')->willReturn('default');
        $this->serviceRegistry->expects($this->once())->method('get')->with('default')->willReturn($this->calculator);
        $this->calculator->expects($this->once())->method('calculate')->with(100, $this->taxRate)->willReturn(23.00);

        $this->assertSame(23.00, $this->delegatingCalculator->calculate(100, $this->taxRate));
    }
}
