<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PercentageDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->beConstructedWith($distributor, $unitsPromotionAdjustmentsApplicator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PercentageDiscountPromotionActionCommand::class);
    }

    function it_implements_a_promotion_action_interface()
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_uses_distributor_and_applicator_to_execute_promotion_action(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order->countItems()->willReturn(2);
        $order->getItems()->willReturn([$firstItem, $secondItem]);

        $firstItem->getTotal()->willReturn(200);
        $secondItem->getTotal()->willReturn(800);

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $distributor->distribute([200, 800], -1000)->willReturn([-200, -800]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-200, -800])->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion);
    }

    function it_does_nothing_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion)
    {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion);
    }

    function it_does_nothing_if_adjustment_amount_would_be_0(
        ProportionalIntegerDistributorInterface $distributor,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(0);
        $distributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion);
    }

    function it_throws_an_exception_if_configuration_is_invalid(OrderInterface $order, PromotionInterface $promotion)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$order, [], $promotion])
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$order, ['percentage' => 'string'], $promotion])
        ;
    }

    function it_throws_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, [], $promotion])
        ;
    }

    function it_reverts_order_units_order_promotion_adjustments(
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        OrderInterface $order,
        OrderItemInterface $item,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(1);
        $order->getItems()->willReturn([$item]);

        $item->getUnits()->willReturn([$unit]);

        $unit
            ->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn([$firstAdjustment, $secondAdjustment])
        ;

        $promotion->getCode()->willReturn('PROMOTION');

        $firstAdjustment->getOriginCode()->willReturn('PROMOTION');
        $secondAdjustment->getOriginCode()->willReturn('OTHER_PROMOTION');

        $unit->removeAdjustment($firstAdjustment)->shouldBeCalled();
        $unit->removeAdjustment($secondAdjustment)->shouldNotBeCalled();

        $this->revert($order, [], $promotion);
    }

    function it_does_not_revert_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion)
    {
        $order->countItems()->willReturn(0);
        $order->getItems()->shouldNotBeCalled();

        $this->revert($order, [], $promotion);
    }

    function it_throws_an_exception_while_reverting_subject_which_is_not_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, [], $promotion])
        ;
    }
}
