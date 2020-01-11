<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\StateResolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderStateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_implements_a_state_resolver_interface(): void
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_an_order_as_fulfilled_when_its_paid_for_and_has_been_shipped(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply(OrderTransitions::TRANSITION_FULFILL)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_does_not_mark_an_order_as_fulfilled_when_it_has_been_paid_but_not_shipped(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $order->getShippingState()->willReturn(Argument::not(OrderShippingStates::STATE_SHIPPED));
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply(OrderTransitions::TRANSITION_FULFILL)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_not_mark_an_order_as_fulfilled_when_it_has_been_shipped_but_not_paid(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);
        $order->getPaymentState()->willReturn(Argument::not(OrderPaymentStates::STATE_PAID));

        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderTransitions::TRANSITION_FULFILL)->willReturn(true);
        $stateMachine->apply(OrderTransitions::TRANSITION_FULFILL)->shouldNotBeCalled();

        $this->resolve($order);
    }
}
