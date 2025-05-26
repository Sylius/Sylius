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

namespace Tests\Sylius\Component\Core\Promotion\Filter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Filter\PriceRangeFilter;

final class PriceRangeFilterTest extends TestCase
{
    private MockObject&ProductVariantPricesCalculatorInterface $productVariantPricesCalculator;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderItemInterface $firstItem;

    private MockObject&OrderItemInterface $secondItem;

    private MockObject&OrderItemInterface $thirdItem;

    private MockObject&ProductVariantInterface $firstItemVariant;

    private MockObject&ProductVariantInterface $secondItemVariant;

    private MockObject&ProductVariantInterface $thirdItemVariant;

    private PriceRangeFilter $filter;

    protected function setUp(): void
    {
        $this->productVariantPricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->firstItem = $this->createMock(OrderItemInterface::class);
        $this->secondItem = $this->createMock(OrderItemInterface::class);
        $this->thirdItem = $this->createMock(OrderItemInterface::class);
        $this->firstItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->secondItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->thirdItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->filter = new PriceRangeFilter($this->productVariantPricesCalculator);
    }

    public function testImplementFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testShouldFilterItemsWhichHasProductWithPriceThatFitsInConfiguredRange(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->thirdItem->expects($this->once())->method('getVariant')->willReturn($this->thirdItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(3))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 500],
                [$this->secondItemVariant, ['channel' => $this->channel], 5000],
                [$this->thirdItemVariant, ['channel' => $this->channel], 15000],
            ]);

        $this->assertEquals(
            [$this->secondItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem, $this->thirdItem],
                [
                    'filters' => ['price_range_filter' => ['min' => 1000, 'max' => 10000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldFilterItemsWhichHasProductWithPriceEqualToMinimumCriteria(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 1000],
                [$this->secondItemVariant, ['channel' => $this->channel], 15000],
            ]);

        $this->assertEquals(
            [$this->firstItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem],
                [
                    'filters' => ['price_range_filter' => ['min' => 1000, 'max' => 10000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldFilterItemsWhichHasProductWithPriceEqualToMaximumCriteria(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 500],
                [$this->secondItemVariant, ['channel' => $this->channel], 10000],
            ]);

        $this->assertEquals(
            [$this->secondItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem],
                [
                    'filters' => ['price_range_filter' => ['min' => 1000, 'max' => 10000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldFilterItemsWhichHasProductWithPriceThatIsBiggerThanConfiguredMinimumCriteria(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->thirdItem->expects($this->once())->method('getVariant')->willReturn($this->thirdItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(3))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 500],
                [$this->secondItemVariant, ['channel' => $this->channel], 5000],
                [$this->thirdItemVariant, ['channel' => $this->channel], 15000],
            ]);

        $this->assertEquals(
            [$this->secondItem, $this->thirdItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem, $this->thirdItem],
                [
                    'filters' => ['price_range_filter' => ['min' => 1000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldFilterItemsWhichHasProductWithPriceEqualToConfiguredMinimumCriteria(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 500],
                [$this->secondItemVariant, ['channel' => $this->channel], 1000],
            ]);

        $this->assertEquals(
            [$this->secondItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem],
                [
                    'filters' => ['price_range_filter' => ['min' => 1000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldFilterItemsWhichHasProductWithPriceThatIsBiggerThanConfiguredMaximumCriteria(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->thirdItem->expects($this->once())->method('getVariant')->willReturn($this->thirdItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(3))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 500],
                [$this->secondItemVariant, ['channel' => $this->channel], 5000],
                [$this->thirdItemVariant, ['channel' => $this->channel], 10000],
            ]);

        $this->assertEquals(
            [$this->firstItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem, $this->thirdItem],
                [
                    'filters' => ['price_range_filter' => ['max' => 1000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldFilterItemsWhichHasProductWithPriceEqualToConfiguredMaximumCriteria(): void
    {
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($this->firstItemVariant);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($this->secondItemVariant);
        $this->productVariantPricesCalculator
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturnMap([
                [$this->firstItemVariant, ['channel' => $this->channel], 500],
                [$this->secondItemVariant, ['channel' => $this->channel], 1000],
            ]);

        $this->assertEquals(
            [$this->firstItem, $this->secondItem],
            $this->filter->filter(
                [$this->firstItem, $this->secondItem],
                [
                    'filters' => ['price_range_filter' => ['max' => 1000]],
                    'channel' => $this->channel,
                ],
            ),
        );
    }

    public function testShouldReturnAllItemsIfConfigurationIsInvalid(): void
    {
        $this->assertEquals(
            [$this->firstItem, $this->secondItem],
            $this->filter->filter([$this->firstItem, $this->secondItem], []),
        );
    }

    public function testShouldThrowExceptionIfChannelIsNotConfigured(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->filter->filter([$this->firstItem, $this->secondItem], ['filters' => ['price_range_filter' => ['min' => 1000]]]);
    }
}
