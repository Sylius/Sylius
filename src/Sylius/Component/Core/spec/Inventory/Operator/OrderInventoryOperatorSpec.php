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

namespace spec\Sylius\Component\Core\Inventory\Operator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderPaymentStates;

final class OrderInventoryOperatorSpec extends ObjectBehavior
{
    function it_implements_an_order_inventory_operator_interface(): void
    {
        $this->shouldImplement(OrderInventoryOperatorInterface::class);
    }

    function it_increases_on_hold_quantity_during_holding(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
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
        ProductVariantInterface $variant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
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
        ProductVariantInterface $variant,
    ): void {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(10);

        $variant->getName()->willReturn('Red Skirt');

        $variant->setOnHold(0)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_increases_on_hand_during_cancelling_of_a_paid_order(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHand()->willReturn(0);

        $variant->setOnHand(10)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_increases_on_hand_during_cancelling_of_a_refunded_order(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_REFUNDED);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHand()->willReturn(0);

        $variant->setOnHand(10)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_throws_an_invalid_argument_exception_if_difference_between_on_hold_and_item_quantity_is_smaller_than_zero_during_cancelling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(5);

        $variant->getName()->willReturn('Red Skirt');

        $this->shouldThrow(\InvalidArgumentException::class)->during('cancel', [$order]);
    }

    function it_throws_an_invalid_argument_exception_if_difference_between_on_hold_and_item_quantity_is_smaller_than_zero_during_selling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(5);

        $variant->getName()->willReturn('Red Skirt');

        $this->shouldThrow(\InvalidArgumentException::class)->during('sell', [$order]);
    }

    function it_throws_an_invalid_argument_exception_if_difference_between_on_hand_and_item_quantity_is_smaller_than_zero_during_selling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);

        $orderItem->getQuantity()->willReturn(10);
        $variant->getOnHold()->willReturn(10);
        $variant->getOnHand()->willReturn(5);

        $variant->getName()->willReturn('Red Skirt');

        $this->shouldThrow(\InvalidArgumentException::class)->during('sell', [$order]);
    }

    function it_does_nothing_if_variant_is_not_tracked_during_cancelling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(false);

        $variant->setOnHold(Argument::any())->shouldNotBeCalled();

        $this->cancel($order);
    }

    function it_does_nothing_if_variant_is_not_tracked_and_order_is_paid_during_cancelling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(false);

        $variant->setOnHand(Argument::any())->shouldNotBeCalled();

        $this->cancel($order);
    }

    function it_does_nothing_if_variant_is_not_tracked_during_holding(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(false);

        $variant->setOnHold(Argument::any())->shouldNotBeCalled();

        $this->hold($order);
    }

    function it_does_nothing_if_variant_is_not_tracked_during_selling(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(false);

        $variant->setOnHold(Argument::any())->shouldNotBeCalled();
        $variant->setOnHand(Argument::any())->shouldNotBeCalled();

        $this->sell($order);
    }
}
