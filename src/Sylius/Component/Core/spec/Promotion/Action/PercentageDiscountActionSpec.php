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
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountAction;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @mixin PercentageDiscountAction
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PercentageDiscountActionSpec extends ObjectBehavior
{
    function let(
        IntegerDistributorInterface $integerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->beConstructedWith($integerDistributor, $unitsPromotionAdjustmentsApplicator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\PercentageDiscountAction');
    }

    function it_implements_promotion_action_interface()
    {
        $this->shouldImplement(PromotionActionInterface::class);
    }

    function it_uses_distributor_and_applicator_to_execute_promotion_action(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        IntegerDistributorInterface $integerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $integerDistributor->distribute(-1000, 2)->willReturn([-500, -500]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-500, -500])->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion);
    }

    function it_does_nothing_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion)
    {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion);
    }

    function it_does_nothing_if_adjustment_amount_would_be_0(
        IntegerDistributorInterface $integerDistributor,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(0);
        $integerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion);
    }

    function it_throws_exception_if_configuration_is_invalid(OrderInterface $order, PromotionInterface $promotion)
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
        $order->getItems()->willReturn(new \ArrayIterator([$item->getWrappedObject()]));

        $item->getUnits()->willReturn(new \ArrayIterator([$unit->getWrappedObject()]));

        $unit
            ->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new \ArrayIterator([$firstAdjustment->getWrappedObject(), $secondAdjustment->getWrappedObject()]))
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

    function it_throws_exception_while_reverting_subject_which_is_not_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, [], $promotion])
        ;
    }

    function it_has_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_percentage_discount_configuration');
    }
}
