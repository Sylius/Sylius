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
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class FixedDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        CurrencyConverterInterface $currencyConverter
    ) {
        $this->beConstructedWith(
            $proportionalIntegerDistributor,
            $unitsPromotionAdjustmentsApplicator,
            $currencyConverter
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FixedDiscountPromotionActionCommand::class);
    }

    function it_implements_promotion_action_interface()
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_uses_a_distributor_and_applicator_to_execute_promotion_action(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order->getCurrencyCode()->willReturn('USD');

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute([6000, 4000], -1000)->willReturn([-600, -400]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-600, -400])->shouldBeCalled();

        $this->execute($order, ['base_amount' => 1000, 'amounts' => []], $promotion);
    }

    function it_does_not_apply_bigger_promotion_than_promotion_subject_total(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $order->getCurrencyCode()->willReturn('USD');

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute([6000, 4000], -10000)->willReturn([-6000, -4000]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-6000, -4000])->shouldBeCalled();

        $this->execute($order, ['base_amount' => 15000, 'amounts' => []], $promotion);
    }

    function it_applies_a_promotion_with_value_in_defined_currency(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        CurrencyConverterInterface $currencyConverter
    ) {
        $order->getCurrencyCode()->willReturn('PLN');

        $currencyConverter->convertToBase(4000, 'PLN')->willReturn(1000);

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute([6000, 4000], -1000)->willReturn([-600, -400]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-600, -400])->shouldBeCalled();

        $this->execute($order, ['base_amount' => 1000, 'amounts' => ['PLN' => 4000]], $promotion);
    }

    function it_does_nothing_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion)
    {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['base_amount' => 1000, 'amounts' => []], $promotion);
    }

    function it_does_nothing_if_subject_total_is_0(
        OrderInterface $order,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    ) {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(0);
        $proportionalIntegerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['base_amount' => 1000, 'amounts' => []], $promotion);
    }

    function it_does_nothing_if_promotion_amount_is_0(
        OrderInterface $order,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    ) {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(1000);
        $proportionalIntegerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['base_amount' => 0, 'amounts' => []], $promotion);
    }

    function it_throws_an_exception_if_configuration_is_invalid(OrderInterface $order, PromotionInterface $promotion)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$order, [], $promotion])
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$order, ['base_amount' => 'string', 'amounts' => []], $promotion])
        ;
    }

    function it_throws_an_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, [], $promotion])
        ;
    }

    function it_reverts_an_order_units_order_promotion_adjustments(
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

        $firstAdjustment->getOriginCode()->willReturn('PROMOTION');
        $secondAdjustment->getOriginCode()->willReturn('OTHER_PROMOTION');

        $promotion->getCode()->willReturn('PROMOTION');

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
