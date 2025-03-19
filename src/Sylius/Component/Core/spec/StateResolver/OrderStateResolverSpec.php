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

namespace spec\Sylius\Component\Core\StateResolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderStateResolverSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine): void
    {
        $this->beConstructedWith($stateMachine);
    }

    function it_implements_a_state_resolver_interface(): void
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_an_order_as_fulfilled_when_its_paid_for_and_has_been_shipped(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_fulfilled_when_its_partially_refunded_and_has_been_shipped(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PARTIALLY_REFUNDED);

        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_does_not_mark_an_order_as_fulfilled_when_it_has_been_paid_but_not_shipped(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $order->getShippingState()->willReturn(Argument::not(OrderShippingStates::STATE_SHIPPED));
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_not_mark_an_order_as_fulfilled_when_it_has_been_shipped_but_not_paid(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);
        $order->getPaymentState()->willReturn(Argument::notIn([
            OrderPaymentStates::STATE_PAID,
            OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
        ]));

        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)->shouldNotBeCalled();

        $this->resolve($order);
    }
}
