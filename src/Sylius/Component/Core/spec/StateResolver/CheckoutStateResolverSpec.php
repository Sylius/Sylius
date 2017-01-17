<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\StateResolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\StateResolver\CheckoutStateResolver;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CheckoutStateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutStateResolver::class);
    }

    function it_implements_an_order_state_resolver_interface()
    {
        $this->shouldImplement(StateResolverInterface::class);
    }


    function it_applies_transition_skip_payment_if_order_total_is_zero_and_this_transition_is_possible(
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(0);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_does_nothing_if_order_total_is_not_zero(
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(10);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_nothing_if_transition_skip_payment_is_not_possible(
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(0);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(false);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }
}
