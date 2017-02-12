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
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\StateResolver\CheckoutStateResolver;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CheckoutStateResolverSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $stateMachineFactory,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ) {
        $this->beConstructedWith($stateMachineFactory, $orderShippingMethodSelectionRequirementChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutStateResolver::class);
    }

    function it_implements_an_order_state_resolver_interface()
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_applies_transition_skip_shipping_and_skip_payment_if_shipping_method_selection_is_not_required_and_its_total_is_0_and_this_transitions_are_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(0);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(false);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_applies_transition_skip_shipping_if_shipping_method_selection_is_not_required_and_this_transition_is_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(10);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(false);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_not_apply_skip_shipping_transition_if_shipping_method_selection_is_required(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(10);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_not_apply_skip_shipping_transition_if_it_is_not_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(10);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_applies_transition_skip_payment_if_order_total_is_zero_and_this_transition_is_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(0);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_does_not_apply_skip_payment_transition_if_order_total_is_not_zero(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(10);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_not_apply_skip_payment_transition_if_transition_skip_payment_is_not_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        StateMachineInterface $stateMachine
    ) {
        $order->getTotal()->willReturn(10);

        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(false);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(false);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }
}
