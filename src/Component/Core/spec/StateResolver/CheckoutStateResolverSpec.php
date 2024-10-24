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
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class CheckoutStateResolverSpec extends ObjectBehavior
{
    function let(
        StateMachineInterface $stateMachine,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $this->beConstructedWith(
            $stateMachine,
            $orderPaymentMethodSelectionRequirementChecker,
            $orderShippingMethodSelectionRequirementChecker,
        );
    }

    function it_implements_an_order_state_resolver_interface(): void
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_applies_transition_skip_shipping_and_skip_payment_if_shipping_method_selection_is_not_required_and_payment_method_selection_is_not_required_and_this_transitions_are_possible(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(false);
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(false);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldBeCalled()
        ;

        $this->resolve($order);
    }

    function it_applies_transition_skip_shipping_if_shipping_method_selection_is_not_required_and_this_transition_is_possible(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(false);
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldNotBeCalled();
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldNotBeCalled()
        ;

        $this->resolve($order);
    }

    function it_does_not_apply_skip_shipping_transition_if_shipping_method_selection_is_required(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldNotBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldNotBeCalled()
        ;

        $this->resolve($order);
    }

    function it_does_not_apply_skip_shipping_transition_if_it_is_not_possible(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldNotBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldNotBeCalled()
        ;

        $this->resolve($order);
    }

    function it_applies_transition_skip_payment_if_payment_method_selection_is_not_required_and_this_transition_is_possible(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(false);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldNotBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldBeCalled()
        ;

        $this->resolve($order);
    }

    function it_does_not_apply_skip_payment_transition_if_payment_method_selection_is_required(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldNotBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldNotBeCalled()
        ;

        $this->resolve($order);
    }

    function it_does_not_apply_skip_payment_transition_if_transition_skip_payment_is_not_possible(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ): void {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(false)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->shouldNotBeCalled()
        ;

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(false)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->shouldNotBeCalled()
        ;

        $this->resolve($order);
    }
}
