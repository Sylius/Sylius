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

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

final class AdjustmentOrderProviderSpec extends ObjectBehavior
{
    function it_returns_order_if_adjustment_is_for_an_order(AdjustmentInterface $adjustment, OrderInterface $order): void
    {
        $adjustment->getAdjustable()->willReturn($order);
        $adjustment->getOrder()->willReturn($order);

        $this->provide($adjustment)->shouldReturn($order);
    }

    function it_returns_order_if_adjustment_is_for_an_order_item(
        AdjustmentInterface $adjustment,
        OrderItemInterface $orderItem,
        OrderInterface $order,
    ): void {
        $adjustment->getAdjustable()->willReturn($orderItem);
        $adjustment->getOrderItem()->willReturn($orderItem);
        $orderItem->getOrder()->willReturn($order);

        $this->provide($adjustment)->shouldReturn($order);
    }

    function it_returns_order_if_adjustment_is_for_an_order_item_unit(
        AdjustmentInterface $adjustment,
        OrderItemUnitInterface $orderItemUnit,
        OrderItemInterface $orderItem,
        OrderInterface $order,
    ): void {
        $adjustment->getAdjustable()->willReturn($orderItemUnit);
        $adjustment->getOrderItemUnit()->willReturn($orderItemUnit);
        $orderItemUnit->getOrderItem()->willReturn($orderItem);
        $orderItem->getOrder()->willReturn($order);

        $this->provide($adjustment)->shouldReturn($order);
    }

    function it_returns_null_if_adjustment_is_not_for_known_type(AdjustmentInterface $adjustment): void
    {
        $adjustment->getAdjustable()->willReturn(null);

        $this->provide($adjustment)->shouldReturn(null);
    }
}
