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
use Sylius\Component\Core\Promotion\Action\FixedDiscountAction;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @mixin FixedDiscountAction
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class FixedDiscountActionSpec extends ObjectBehavior
{
    function let(
        OriginatorInterface $originator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->beConstructedWith(
            $originator,
            $proportionalIntegerDistributor,
            $unitsPromotionAdjustmentsApplicator
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\FixedDiscountAction');
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
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute(10000, [6000, 4000], -1000)->willReturn([-600, -400]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-600, -400])->shouldBeCalled();

        $this->execute($order, ['amount' => 1000], $promotion);
    }

    function it_does_not_apply_bigger_promotion_than_promotion_subject_total(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute(10000, [6000, 4000], -10000)->willReturn([-6000, -4000]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-6000, -4000])->shouldBeCalled();

        $this->execute($order, ['amount' => 15000], $promotion);
    }

    function it_does_nothing_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion)
    {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['amount' => 1000], $promotion);
    }

    function it_does_nothing_if_subject_total_is_0(
        OrderInterface $order,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    ) {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(0);
        $proportionalIntegerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['amount' => 1000], $promotion);
    }

    function it_does_nothing_if_promotion_amount_is_0(
        OrderInterface $order,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    ) {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(1000);
        $proportionalIntegerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['amount' => 0], $promotion);
    }

    function it_throws_exception_if_configuration_is_invalid(OrderInterface $order, PromotionInterface $promotion)
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('"amount" must be set and must be an integer.'))
            ->during('execute', [$order, [], $promotion])
        ;

        $this
            ->shouldThrow(new \InvalidArgumentException('"amount" must be set and must be an integer.'))
            ->during('execute', [$order, ['amount' => 'string'], $promotion])
        ;
    }

    function it_throws_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $this
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('execute', [$subject, [], $promotion])
        ;
    }

    function it_reverts_order_units_order_promotion_adjustments(
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        OrderInterface $order,
        OrderItemInterface $item,
        OrderItemUnitInterface $unit,
        OriginatorInterface $originator,
        PromotionInterface $otherPromotion,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(1);
        $order->getItems()->willReturn(new \ArrayIterator([$item->getWrappedObject()]));

        $item->getUnits()->willReturn(new \ArrayIterator([$unit->getWrappedObject()]));

        $unit
            ->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new \ArrayIterator([$firstAdjustment->getWrappedObject(), $secondAdjustment->getWrappedObject()]))
        ;

        $originator->getOrigin($firstAdjustment)->willReturn($promotion);
        $originator->getOrigin($secondAdjustment)->willReturn($otherPromotion);

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
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('revert', [$subject, [], $promotion])
        ;
    }

    function it_has_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_fixed_discount_configuration');
    }
}
