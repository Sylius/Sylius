<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Reverser;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderItemPromotionAdjustmentsReverser;
use Sylius\Component\Core\Promotion\Reverser\OrderItemPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class OrderItemPromotionAdjustmentsReverserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemPromotionAdjustmentsReverser::class);
    }

    function it_implements_order_promotion_adjustments_reverser_interface()
    {
        $this->shouldImplement(OrderItemPromotionAdjustmentsReverserInterface::class);
    }

    function it_reverts_promotion_adjustments_on_all_units_of_given_order(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment
    ) {
        $order->getItems()->shouldBeCalled()->willReturn([$orderItem]);
        $orderItem->getUnits()->shouldBeCalled()->willReturn([$unit1, $unit2]);

        $unit1->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled()->willReturn([$firstAdjustment]);
        $promotion->getCode()->shouldBeCalled()->willReturn('code');
        $firstAdjustment->getOriginCode()->shouldBeCalled()->willReturn('code');
        $unit1->removeAdjustment($firstAdjustment)->shouldBeCalled();

        $unit2->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled()->willReturn([$secondAdjustment]);
        $promotion->getCode()->shouldBeCalled()->willReturn('code');
        $secondAdjustment->getOriginCode()->shouldBeCalled()->willReturn('code');
        $unit2->removeAdjustment($secondAdjustment)->shouldBeCalled();

        $this->revert($order, $promotion);
    }

    function it_does_not_revert_adjustments_when_codes_do_not_match(
        Order $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment
    ) {
        $order->getItems()->shouldBeCalled()->willReturn([$orderItem]);
        $orderItem->getUnits()->shouldBeCalled()->willReturn([$unit1, $unit2]);

        $unit1->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled()->willReturn([$firstAdjustment]);
        $promotion->getCode()->shouldBeCalled()->willReturn('code1');
        $firstAdjustment->getOriginCode()->shouldBeCalled()->willReturn('code2');
        $unit1->removeAdjustment($firstAdjustment)->shouldNotBeCalled();

        $unit2->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled()->willReturn([$secondAdjustment]);
        $promotion->getCode()->shouldBeCalled()->willReturn('code1');
        $secondAdjustment->getOriginCode()->shouldBeCalled()->willReturn('code2');
        $unit2->removeAdjustment($secondAdjustment)->shouldNotBeCalled();

        $this->revert($order, $promotion);
    }
}
