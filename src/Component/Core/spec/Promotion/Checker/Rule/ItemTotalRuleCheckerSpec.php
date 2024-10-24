<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Promotion\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class ItemTotalRuleCheckerSpec extends ObjectBehavior
{
    function it_is_be_a_rule_checker(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_a_subject_as_not_eligible_if_the_subject_total_is_less_than_configured(
        ChannelInterface $channel,
        OrderInterface $order,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');
        $order->getPromotionSubjectTotal()->willReturn(400);

        $this->isEligible($order, ['WEB_US' => ['amount' => 500]])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_eligible_if_the_subject_total_is_greater_than_configured(
        ChannelInterface $channel,
        OrderInterface $order,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');
        $order->getPromotionSubjectTotal()->willReturn(600);

        $this->isEligible($order, ['WEB_US' => ['amount' => 500]])->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_eligible_if_the_subject_total_is_equal_with_configured(
        ChannelInterface $channel,
        OrderInterface $order,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');
        $order->getPromotionSubjectTotal()->willReturn(500);

        $this->isEligible($order, ['WEB_US' => ['amount' => 500]])->shouldReturn(true);
    }

    function it_returns_false_if_there_is_no_configuration_for_order_channel(
        ChannelInterface $channel,
        OrderInterface $order,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_throws_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $promotionSubject): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isEligible', [$promotionSubject, []])
        ;
    }
}
