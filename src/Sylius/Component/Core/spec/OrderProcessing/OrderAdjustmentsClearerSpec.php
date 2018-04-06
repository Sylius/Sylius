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

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderAdjustmentsClearerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::TAX_ADJUSTMENT,
        ]);
    }
    
    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_removes_adjustments_from_order_recursively(OrderInterface $order): void
    {
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $this->process($order);
    }
}
