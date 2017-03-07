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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\OrderItemPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderItemPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class UnitPercentageDiscountPromotionActionCommandSpec extends ObjectBehavior
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
        $this->shouldHaveType(UnitPercentageDiscountPromotionActionCommand::class);
    }

    function it_applies_percentage_discount_on_every_unit_in_order(
        ChannelInterface $channel,
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        Collection $originalItems,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        PromotionInterface $promotion,
        OrderItemPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn($originalItems);
        $originalItems->toArray()->willReturn([$orderItem1]);

        $priceRangeFilter->filter([$orderItem1], ['percentage' => 0.2, 'channel' => $channel])->willReturn([$orderItem1]);
        $taxonFilter->filter([$orderItem1], ['percentage' => 0.2])->willReturn([$orderItem1]);
        $productFilter->filter([$orderItem1], ['percentage' => 0.2])->willReturn([$orderItem1]);

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnitPrice()->willReturn(500);

        $adjustmentsApplicator->apply($orderItem1, $promotion, 100)->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['percentage' => 0.2]], $promotion)->shouldReturn(true);
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

        $priceRangeFilter->filter([$orderItem], ['percentage' => 0.2, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['percentage' => 0.2])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['percentage' => 0.2])->willReturn([]);

        $this->execute($order, ['WEB_US' => ['percentage' => 0.2]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_configuration_for_order_channel_is_not_defined(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_PL');

        $order->getItems()->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['percentage' => 0.2]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_percentage_configuration_not_defined(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_PL');

        $order->getItems()->shouldNotBeCalled();

        $this->execute($order, ['WEB_PL' => []], $promotion)->shouldReturn(false);
    }

    function it_throws_an_exception_if_passed_subject_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

    function it_reverts_a_proper_promotion_adjustment_from_all_units(
        OrderItemPromotionAdjustmentsReverserInterface $adjustmentsReverser,
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $adjustmentsReverser->revert($order, $promotion)->shouldBeCalled();

        $this->revert($order, ['percentage' => 0.2], $promotion);
    }

    function it_throws_an_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }
}
