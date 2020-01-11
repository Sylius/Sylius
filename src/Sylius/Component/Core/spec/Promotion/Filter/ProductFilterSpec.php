<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Promotion\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;

final class ProductFilterSpec extends ObjectBehavior
{
    function it_implements_a_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_passed_order_items_with_given_configuration(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductInterface $product1,
        ProductInterface $product2
    ): void {
        $item1->getProduct()->willReturn($product1);
        $product1->getCode()->willReturn('product1');

        $item2->getProduct()->willReturn($product2);
        $product2->getCode()->willReturn('product2');

        $this->filter([$item1, $item2], ['filters' => ['products_filter' => ['products' => ['product1']]]])->shouldReturn([$item1]);
    }

    function it_returns_all_items_if_configuration_is_invalid(OrderItemInterface $item): void
    {
        $this->filter([$item], [])->shouldReturn([$item]);
    }

    function it_returns_all_items_if_configuration_is_empty(OrderItemInterface $item): void
    {
        $this->filter([$item], ['filters' => ['products_filter' => ['products' => []]]])->shouldReturn([$item]);
    }
}
