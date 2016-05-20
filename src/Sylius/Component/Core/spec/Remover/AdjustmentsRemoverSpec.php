<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Remover;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Remover\AdjustmentsRemover;
use Sylius\Component\Core\Remover\AdjustmentsRemoverInterface;

/**
 * @mixin AdjustmentsRemover
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AdjustmentsRemoverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Remover\AdjustmentsRemover');
    }

    function it_implements_adjustments_remover_interface()
    {
        $this->shouldImplement(AdjustmentsRemoverInterface::class);
    }

    function it_removes_adjustments_from_order_recursively(OrderInterface $order)
    {
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $order->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $this->removeFrom($order);
    }
}
