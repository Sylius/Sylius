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
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountAction;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin FixedDiscountAction
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountActionSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->beConstructedWith(
            $adjustmentFactory,
            $originator,
            $proportionalIntegerDistributor,
            $unitsPromotionAdjustmentsApplicator
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\FixedDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement(PromotionActionInterface::class);
    }

    function it_applies_fixed_discount_as_promotion_adjustment(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(5000);

        $firstItem->getTotal()->willReturn(3000);
        $secondItem->getTotal()->willReturn(2000);

        $proportionalIntegerDistributor->distribute(5000, [3000, 2000], -500)->willReturn([-300, -200]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-300, -200]);

        $this->execute($order, ['amount' => 500], $promotion);
    }

    function it_does_not_applies_bigger_discount_than_promotion_subject_total(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(5000);

        $firstItem->getTotal()->willReturn(3000);
        $secondItem->getTotal()->willReturn(2000);

        $proportionalIntegerDistributor->distribute(5000, [3000, 2000], -5000)->willReturn([-3000, -2000]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-3000, -2000]);

        $this->execute($order, ['amount' => 10000], $promotion);
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
}
