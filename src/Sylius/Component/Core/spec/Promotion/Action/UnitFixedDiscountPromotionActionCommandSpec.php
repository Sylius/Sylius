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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\OrderItemPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderItemPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class UnitFixedDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        OrderItemPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        OrderItemPromotionAdjustmentsReverserInterface $adjustmentsReverser,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter
    ) {
        $this->beConstructedWith(
            $adjustmentsApplicator,
            $adjustmentsReverser,
            $priceRangeFilter,
            $taxonFilter,
            $productFilter
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnitFixedDiscountPromotionActionCommand::class);
    }

    function it_applies_a_fixed_discount_on_every_unit_in_order(
        OrderItemPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        ChannelInterface $channel,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));
        $order->getChannel()->willReturn($channel);

        $priceRangeFilter->filter([$orderItem], ['amount' => 500, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['amount' => 500])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['amount' => 500])->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $adjustmentsApplicator->apply($orderItem, $promotion, 500)->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 500]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_a_discount_if_all_items_have_been_filtered_out(
        ChannelInterface $channel,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PromotionInterface $promotion
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));
        $order->getChannel()->willReturn($channel);

        $priceRangeFilter->filter([$orderItem], ['amount' => 500, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['amount' => 500])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['amount' => 500])->willReturn([]);

        $this->execute($order, ['WEB_US' => ['amount' => 500]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_with_amount_0(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
        OrderItemPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $adjustmentsApplicator->apply(Argument::cetera())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 0]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_no_amount_is_defined_for_order_channel(
        ChannelInterface $channel,
        FactoryInterface $adjustmentFactory,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $this->execute($order, ['WEB_PL' => ['amount' => 0]], $promotion)->shouldReturn(false);
    }

    function it_throws_an_exception_if_passed_subject_to_execute_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, ['amount' => 1000], $promotion])
        ;
    }

    function it_reverts_a_proper_promotion_adjustment_from_all_units(
        OrderItemPromotionAdjustmentsReverserInterface $adjustmentsReverser,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $adjustmentsReverser->revert($order, $promotion);

        $this->revert($order, [], $promotion);
    }

    function it_throws_an_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, ['amount' => 1000], $promotion])
        ;
    }
}
