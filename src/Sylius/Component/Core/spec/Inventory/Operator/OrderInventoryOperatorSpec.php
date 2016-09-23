<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Inventory\Operator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperator;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;

/**
 * @mixin OrderInventoryOperator
 *
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderInventoryOperatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderInventoryOperator::class);
    }

    function it_implements_order_inventory_operator_interface()
    {
        $this->shouldImplement(OrderInventoryOperatorInterface::class);
    }

    function it_increases_on_hold_quantity_during_holding(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(0);

        $variant->setOnHold(10)->shouldBeCalled();

        $this->hold($order);
    }

    function it_decreases_on_hold_and_on_hand_during_selling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(10);
        $variant->getOnHand()->willReturn(10);


        $variant->getName()->willReturn('Red Skirt');

        $variant->setOnHold(0)->shouldBeCalled();
        $variant->setOnHand(0)->shouldBeCalled();

        $this->sell($order);
    }

    function it_decreases_on_hold_quantity_during_cancelling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(10);

        $variant->getName()->willReturn('Red Skirt');

        $variant->setOnHold(0)->shouldBeCalled();

        $this->cancel($order);
    }


    function it_increases_on_hand_during_cancelling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHand()->willReturn(0);

        $variant->setOnHand(10)->shouldBeCalled();

        $this->cancel($order);
    }


    function it_throws_invalid_argument_exception_if_difference_between_on_hold_and_item_quantity_is_smaller_than_zero_during_cancelling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(5);

        $variant->getName()->willReturn('Red Skirt');

        $this->shouldThrow(\InvalidArgumentException::class)->during('cancel', [$order]);
    }

    function it_throws_invalid_argument_exception_if_difference_between_on_hold_and_item_quantity_is_smaller_than_zero_during_selling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(5);

        $variant->getName()->willReturn('Red Skirt');

        $this->shouldThrow(\InvalidArgumentException::class)->during('sell', [$order]);
    }

    function it_throws_invalid_argument_exception_if_difference_between_on_hand_and_item_quantity_is_smaller_than_zero_during_selling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(10);
        $variant->getOnHand()->willReturn(5);

        $variant->getName()->willReturn('Red Skirt');
        
        $this->shouldThrow(\InvalidArgumentException::class)->during('sell', [$order]);
    }
}