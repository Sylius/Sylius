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

namespace spec\Sylius\Component\Core\Promotion\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;

final class PriceRangeFilterSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $productVariantPricesCalculator): void
    {
        $this->beConstructedWith($productVariantPricesCalculator);
    }

    function it_implements_a_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_items_which_has_product_with_price_that_fits_in_configured_range(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantInterface $item3Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);
        $item3->getVariant()->willReturn($item3Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(500);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(5000);
        $productVariantPricesCalculator->calculate($item3Variant, ['channel' => $channel])->willReturn(15000);

        $this
            ->filter([$item1, $item2, $item3], [
                'filters' => ['price_range_filter' => ['min' => 1000, 'max' => 10000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item2])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_minimum_criteria(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(1000);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(15000);

        $this
            ->filter([$item1, $item2], [
                'filters' => ['price_range_filter' => ['min' => 1000, 'max' => 10000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item1])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_maximum_criteria(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(500);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(10000);

        $this
            ->filter([$item1, $item2], [
                'filters' => ['price_range_filter' => ['min' => 1000, 'max' => 10000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item2])
        ;
    }

    function it_filters_items_which_has_product_with_price_that_is_bigger_than_configured_minimum_criteria(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantInterface $item3Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);
        $item3->getVariant()->willReturn($item3Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(500);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(5000);
        $productVariantPricesCalculator->calculate($item3Variant, ['channel' => $channel])->willReturn(10000);

        $this
            ->filter([$item1, $item2, $item3], [
                'filters' => ['price_range_filter' => ['min' => 1000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item2, $item3])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_configured_minimum_criteria(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(500);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(1000);

        $this
            ->filter([$item1, $item2], [
                'filters' => ['price_range_filter' => ['min' => 1000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item2])
        ;
    }

    function it_filters_items_which_has_product_with_price_that_is_bigger_than_configured_maximum_criteria(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantInterface $item3Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);
        $item3->getVariant()->willReturn($item3Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(500);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(5000);
        $productVariantPricesCalculator->calculate($item3Variant, ['channel' => $channel])->willReturn(10000);

        $this
            ->filter([$item1, $item2, $item3], [
                'filters' => ['price_range_filter' => ['max' => 1000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item1])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_configured_maximum_criteria(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $productVariantPricesCalculator->calculate($item1Variant, ['channel' => $channel])->willReturn(500);
        $productVariantPricesCalculator->calculate($item2Variant, ['channel' => $channel])->willReturn(1000);

        $this
            ->filter([$item1, $item2], [
                'filters' => ['price_range_filter' => ['max' => 1000]],
                'channel' => $channel,
            ])
            ->shouldReturn([$item1, $item2])
        ;
    }

    function it_returns_all_items_if_configuration_is_invalid(
        ChannelInterface $channel,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
    ): void {
        $this->filter([$item1, $item2], [])->shouldReturn([$item1, $item2]);
    }

    function it_throws_exception_if_channel_is_not_configured(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('filter', [[$item1, $item2], ['filters' => ['price_range_filter' => ['min' => 1000]]]])
        ;
    }
}
