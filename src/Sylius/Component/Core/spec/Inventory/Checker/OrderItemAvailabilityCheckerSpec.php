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

namespace spec\Sylius\Component\Core\Inventory\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class OrderItemAvailabilityCheckerSpec extends ObjectBehavior
{
    function it_implements_an_order_item_availability_checker_interface(): void
    {
        $this->shouldImplement(OrderItemAvailabilityCheckerInterface::class);
    }

    function it_returns_true_if_variant_is_untracked(
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(false);

        $this->isReservedStockSufficient($orderItem)->shouldReturn(true);
    }

    function it_returns_true_if_stock_is_sufficient(
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(true);
        $variant->getOnHold()->willReturn(2);
        $variant->getOnHand()->willReturn(2);

        $this->isReservedStockSufficient($orderItem)->shouldReturn(true);
    }

    function it_returns_false_if_on_hold_value_is_not_sufficient(
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(true);
        $variant->getOnHold()->willReturn(1);
        $variant->getOnHand()->willReturn(2);

        $this->isReservedStockSufficient($orderItem)->shouldReturn(false);
    }

    function it_returns_false_if_on_hand_value_is_not_sufficient(
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(true);
        $variant->getOnHold()->willReturn(2);
        $variant->getOnHand()->willReturn(1);

        $this->isReservedStockSufficient($orderItem)->shouldReturn(false);
    }
}
