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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\OrderPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Reverser\OrderPromotionAdjustmentsReverserInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class FixedDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser
    )
    {
        $this->beConstructedWith(
            $proportionalIntegerDistributor,
            $adjustmentsApplicator,
            $adjustmentsReverser
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
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
    )
    {
        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]));

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute([6000, 4000], -1000)->willReturn([-600, -400]);
        $adjustmentsApplicator->apply($order, $promotion, [-600, -400])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_bigger_discount_than_promotion_subject_total(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator
    )
    {
        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]));

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $secondItem->getTotal()->willReturn(4000);

        $proportionalIntegerDistributor->distribute([6000, 4000], -10000)->willReturn([-6000, -4000]);
        $adjustmentsApplicator->apply($order, $promotion, [-6000, -4000])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 15000]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_discount_if_order_has_no_items(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    )
    {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_subject_total_is_0(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    )
    {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(0);
        $proportionalIntegerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_promotion_amount_is_0(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    )
    {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(1000);
        $proportionalIntegerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 0]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_amount_for_order_channel_is_not_configured(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    )
    {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(1);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['WEB_PL' => ['amount' => 1000]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_configuration_is_invalid(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    )
    {
        $order->getChannel()->willReturn($channel, $channel);
        $channel->getCode()->willReturn('WEB_US', 'WEB_US');
        $order->countItems()->willReturn(1, 1);

        $this->execute($order, ['WEB_US' => []], $promotion)->shouldReturn(false);
        $this->execute($order, ['WEB_US' => ['amount' => 'string']], $promotion)->shouldReturn(false);
    }

    function it_throws_an_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    )
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, [], $promotion]);
    }

    function it_reverts_an_order_units_order_promotion_adjustments(
        OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser,
        OrderInterface $order,
        PromotionInterface $promotion
    )
    {
        $adjustmentsReverser->revert($order, $promotion);

        $this->revert($order, [], $promotion);
    }

    function it_does_not_revert_if_order_has_no_items(
        OrderPromotionAdjustmentsReverserInterface $adjustmentsReverser,
        OrderInterface $order,
        PromotionInterface $promotion
    )
    {
        $order->countItems()->willReturn(0);
        $adjustmentsReverser->revert($order, $promotion)->shouldNotBeCalled();

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
