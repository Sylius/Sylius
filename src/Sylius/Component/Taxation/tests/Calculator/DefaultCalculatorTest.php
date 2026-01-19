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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Calculator\DefaultCalculator;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class DefaultCalculatorTest extends TestCase
{
    /** @var TaxRateInterface&MockObject */
    private MockObject $taxRate;

    private DefaultCalculator $calculator;

    protected function setUp(): void
    {
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->calculator = new DefaultCalculator();
    }

    public function testShouldImplementSyliusTaxCalculatorInterface(): void
    {
        $this->assertInstanceOf(CalculatorInterface::class, $this->calculator);
    }

    #[DataProvider('provideRateExcludedData')]
    public function testShouldCalculateTaxAsPercentageOfGivenBaseIfRateIsNotIncludedInPrice(
        int $base,
        float $expectedResult,
    ): void {
        $this->taxRate->expects($this->once())->method('isIncludedInPrice')->willReturn(false);
        $this->taxRate->expects($this->once())->method('getAmount')->willReturn(0.23);

        $this->assertSame($expectedResult, $this->calculator->calculate($base, $this->taxRate));
    }

    /** @return iterable<array<int|float>> */
    public static function provideRateExcludedData(): iterable
    {
        yield [10000, 2300.00];
        yield [100000, 23000.00];
        yield [249599, 57408.00];
        yield [321454, 73934.00];
    }

    #[DataProvider('provideRateIncludedData')]
    public function testShouldCalculateCorrectTaxForGivenBaseIfRateIsIncludedInPrice(
        float $taxRateAmount,
        int $base,
        float $expectedResult,
    ): void {
        $this->taxRate->expects($this->once())->method('isIncludedInPrice')->willReturn(true);
        $this->taxRate->expects($this->once())->method('getAmount')->willReturn($taxRateAmount);

        $this->assertSame($expectedResult, $this->calculator->calculate($base, $this->taxRate));
    }

    /** @return iterable<array<int|float>> */
    public static function provideRateIncludedData(): iterable
    {
        yield [0.23, 10000, 1870.00];
        yield [0.23, 500, 93.00];
        yield [0.20, 10000, 1667.00];
        yield [0.20, 315, 53.00];
    }
}
