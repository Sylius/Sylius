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

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderAdjustmentsClearerSpec extends ObjectBehavior
{
    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_removes_adjustments_with_default_types_from_order_recursively(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(true);

        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $this->process($order);
    }

    function it_removes_adjustments_with_specified_types_from_order_recursively(OrderInterface $order): void
    {
        $this->beConstructedWith([
            AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
        ]);

        $order->canBeProcessed()->willReturn(true);

        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_cannot_be_processed(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(false);

        $order->removeAdjustmentsRecursively(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }
}
