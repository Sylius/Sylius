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

namespace spec\Sylius\Component\Core\Shipping\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

final class OrderTotalGreaterThanOrEqualRuleCheckerSpec extends ObjectBehavior
{
    function it_implements_rule_checker_interface(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderTotalGreaterThanOrEqualRuleChecker::class);
    }

    function it_denies_subject_if_subject_is_not_a_core_shipment(BaseShipmentInterface $shipment): void
    {
        $this->isEligible($shipment, [])->shouldReturn(false);
    }

    function it_recognizes_subject_if_order_total_is_greater_than_configured_amount(
        ShipmentInterface $subject,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $subject->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getItemsTotal()->willReturn(101);
        $channel->getCode()->willReturn('CHANNEL');

        $this->isEligible($subject, [
            'CHANNEL' => [
                'amount' => 100,
            ],
        ])->shouldReturn(true);
    }

    function it_recognizes_subject_if_order_total_is_equal_to_configured_amount(
        ShipmentInterface $subject,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $subject->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getItemsTotal()->willReturn(100);
        $channel->getCode()->willReturn('CHANNEL');

        $this->isEligible($subject, [
            'CHANNEL' => [
                'amount' => 100,
            ],
        ])->shouldReturn(true);
    }

    function it_denies_subject_if_order_total_is_less_than_configured_amount(
        ShipmentInterface $subject,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $subject->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getItemsTotal()->willReturn(99);
        $channel->getCode()->willReturn('CHANNEL');

        $this->isEligible($subject, [
            'CHANNEL' => [
                'amount' => 100,
            ],
        ])->shouldReturn(false);
    }
}
