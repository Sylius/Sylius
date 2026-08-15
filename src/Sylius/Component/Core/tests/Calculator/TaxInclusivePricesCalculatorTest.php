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

namespace Tests\Sylius\Component\Core\Calculator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Calculator\TaxInclusivePricesCalculator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

#[CoversClass(TaxInclusivePricesCalculator::class)]
final class TaxInclusivePricesCalculatorTest extends TestCase
{
    private MockObject&ProductVariantPricesCalculatorInterface $inner;

    private MockObject&TaxRateResolverInterface $taxRateResolver;

    private CalculatorInterface&MockObject $taxCalculator;

    private MockObject&ProductVariantInterface $productVariant;

    private ChannelInterface&MockObject $channel;

    private TaxInclusivePricesCalculator $calculator;

    protected function setUp(): void
    {
        $this->inner = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->taxRateResolver = $this->createMock(TaxRateResolverInterface::class);
        $this->taxCalculator = $this->createMock(CalculatorInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->calculator = new TaxInclusivePricesCalculator(
            $this->inner,
            $this->taxRateResolver,
            $this->taxCalculator,
        );
    }

    public function testItImplementsInterface(): void
    {
        $this->assertInstanceOf(ProductVariantPricesCalculatorInterface::class, $this->calculator);
    }

    public function testItReturnsPriceUnchangedWhenFlagIsDisabled(): void
    {
        $this->channel->method('isShowPricesIncludingTax')->willReturn(false);
        $this->inner->method('calculate')->willReturn(10000);
        $this->taxRateResolver->expects($this->never())->method('resolve');

        $result = $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(10000, $result);
    }

    public function testItReturnsPriceUnchangedWhenNoDefaultTaxZone(): void
    {
        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->channel->method('getDefaultTaxZone')->willReturn(null);
        $this->inner->method('calculate')->willReturn(10000);
        $this->taxRateResolver->expects($this->never())->method('resolve');

        $result = $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(10000, $result);
    }

    public function testItReturnsPriceUnchangedWhenNoTaxRateResolved(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->channel->method('getDefaultTaxZone')->willReturn($zone);
        $this->inner->method('calculate')->willReturn(10000);
        $this->taxRateResolver->method('resolve')->willReturn(null);

        $result = $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(10000, $result);
    }

    public function testItReturnsPriceUnchangedWhenTaxRateIsIncludedInPrice(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('isIncludedInPrice')->willReturn(true);

        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->channel->method('getDefaultTaxZone')->willReturn($zone);
        $this->inner->method('calculate')->willReturn(10000);
        $this->taxRateResolver->method('resolve')->willReturn($taxRate);
        $this->taxCalculator->expects($this->never())->method('calculate');

        $result = $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(10000, $result);
    }

    public function testItAddsTaxToPriceWhenFlagIsEnabled(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('isIncludedInPrice')->willReturn(false);

        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->channel->method('getDefaultTaxZone')->willReturn($zone);
        $this->inner->method('calculate')->willReturn(10000);
        $this->taxRateResolver->method('resolve')->willReturn($taxRate);
        $this->taxCalculator->method('calculate')->with(10000.0, $taxRate)->willReturn(2300.0);

        $result = $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(12300, $result);
    }

    public function testItAddsTaxToOriginalPrice(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('isIncludedInPrice')->willReturn(false);

        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->channel->method('getDefaultTaxZone')->willReturn($zone);
        $this->inner->method('calculateOriginal')->willReturn(12000);
        $this->taxRateResolver->method('resolve')->willReturn($taxRate);
        $this->taxCalculator->method('calculate')->with(12000.0, $taxRate)->willReturn(2760.0);

        $result = $this->calculator->calculateOriginal($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(14760, $result);
    }

    public function testItReturnsNullForLowestPriceWhenInnerReturnsNull(): void
    {
        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->inner->method('calculateLowestPriceBeforeDiscount')->willReturn(null);

        $result = $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, ['channel' => $this->channel]);

        $this->assertNull($result);
    }

    public function testItAddsTaxToLowestPrice(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('isIncludedInPrice')->willReturn(false);

        $this->channel->method('isShowPricesIncludingTax')->willReturn(true);
        $this->channel->method('getDefaultTaxZone')->willReturn($zone);
        $this->inner->method('calculateLowestPriceBeforeDiscount')->willReturn(9000);
        $this->taxRateResolver->method('resolve')->willReturn($taxRate);
        $this->taxCalculator->method('calculate')->with(9000.0, $taxRate)->willReturn(2070.0);

        $result = $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, ['channel' => $this->channel]);

        $this->assertSame(11070, $result);
    }
}
