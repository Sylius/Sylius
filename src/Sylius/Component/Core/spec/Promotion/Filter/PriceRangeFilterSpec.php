<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Filter\PriceRangeFilter;

/**
 * @mixin PriceRangeFilter
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PriceRangeFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PriceRangeFilter::class);
    }

    function it_implements_a_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_items_which_has_product_with_price_that_fits_in_configured_range(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantInterface $item3Variant
    ) {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);
        $item3->getVariant()->willReturn($item3Variant);

        $item1Variant->getPrice()->willReturn(500);
        $item2Variant->getPrice()->willReturn(5000);
        $item3Variant->getPrice()->willReturn(15000);

        $this
            ->filter([$item1, $item2, $item3], ['filters' => ['price_range' => ['min' => 1000, 'max' => 10000]]])
            ->shouldReturn([$item2])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_minimum_criteria(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant
    ) {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $item1Variant->getPrice()->willReturn(1000);
        $item2Variant->getPrice()->willReturn(15000);

        $this
            ->filter([$item1, $item2], ['filters' => ['price_range' => ['min' => 1000, 'max' => 10000]]])
            ->shouldReturn([$item1])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_maximum_criteria(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant
    ) {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $item1Variant->getPrice()->willReturn(500);
        $item2Variant->getPrice()->willReturn(10000);

        $this
            ->filter([$item1, $item2], ['filters' => ['price_range' => ['min' => 1000, 'max' => 10000]]])
            ->shouldReturn([$item2])
        ;
    }

    function it_filters_items_which_has_product_with_price_that_is_bigger_than_configured_minimum_criteria(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant,
        ProductVariantInterface $item3Variant
    ) {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);
        $item3->getVariant()->willReturn($item3Variant);

        $item1Variant->getPrice()->willReturn(500);
        $item2Variant->getPrice()->willReturn(5000);
        $item3Variant->getPrice()->willReturn(10000);

        $this
            ->filter([$item1, $item2, $item3], ['filters' => ['price_range' => ['min' => 1000]]])
            ->shouldReturn([$item2, $item3])
        ;
    }

    function it_filters_items_which_has_product_with_price_equal_to_configured_minimum_criteria(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $item1Variant,
        ProductVariantInterface $item2Variant
    ) {
        $item1->getVariant()->willReturn($item1Variant);
        $item2->getVariant()->willReturn($item2Variant);

        $item1Variant->getPrice()->willReturn(500);
        $item2Variant->getPrice()->willReturn(1000);

        $this
            ->filter([$item1, $item2], ['filters' => ['price_range' => ['min' => 1000]]])
            ->shouldReturn([$item2])
        ;
    }

    function it_returns_all_items_if_configuration_is_invalid(OrderItemInterface $item1, OrderItemInterface $item2)
    {
        $this->filter([$item1, $item2], [])->shouldReturn([$item1, $item2]);
        $this->filter([$item1, $item2], ['filters' => ['price_range' => ['max' => 10000]]])->shouldReturn([$item1, $item2]);
    }
}
