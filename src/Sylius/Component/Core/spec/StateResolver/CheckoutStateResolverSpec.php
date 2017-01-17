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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
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

    function it_applies_transition_skip_shipping_and_skip_payment_if_none_of_order_items_require_shipping_its_total_is_0_and_this_transitions_are_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $order->getTotal()->willReturn(0);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(false);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_applies_transition_skip_shipping_if_none_of_order_items_require_shipping_and_this_transition_is_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $order->getTotal()->willReturn(10);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(false);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_not_apply_skip_shipping_transition_if_at_least_one_product_variant_in_order_requires_shipping(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $order->getTotal()->willReturn(10);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(true);

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
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $order->getTotal()->willReturn(10);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isShippingRequired()->willReturn(false);
        $secondProductVariant->isShippingRequired()->willReturn(true);

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
        OrderItemInterface $firstOrderItem,
        ProductVariantInterface $firstProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem]);
        $order->getTotal()->willReturn(0);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstProductVariant->isShippingRequired()->willReturn(true);

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
        OrderItemInterface $firstOrderItem,
        ProductVariantInterface $firstProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem]);
        $order->getTotal()->willReturn(10);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstProductVariant->isShippingRequired()->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }

    function it_does_nothing_if_transition_skip_payment_is_not_possible(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        ProductVariantInterface $firstProductVariant,
        StateMachineInterface $stateMachine
    ) {
        $order->getItems()->willReturn([$firstOrderItem]);
        $order->getTotal()->willReturn(10);

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstProductVariant->isShippingRequired()->willReturn(true);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->willReturn(false);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)->shouldNotBeCalled();

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->willReturn(false);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)->shouldNotBeCalled();

        $this->resolve($order);
    }
}
