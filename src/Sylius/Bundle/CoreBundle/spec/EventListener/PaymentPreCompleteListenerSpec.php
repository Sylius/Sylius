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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final class PaymentPreCompleteListenerSpec extends ObjectBehavior
{
    function let(OrderItemAvailabilityCheckerInterface $orderItemAvailabilityChecker)
    {
        $this->beConstructedWith($orderItemAvailabilityChecker);
    }

    function it_does_nothing_if_reserved_stock_is_sufficient(
        OrderItemAvailabilityCheckerInterface $orderItemAvailabilityChecker,
        ResourceControllerEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
    ): void {
        $event->getSubject()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItemAvailabilityChecker->isReservedStockSufficient($orderItem)->willReturn(true);

        $event->setMessageType('error')->shouldNotBeCalled();
        $event->setMessage('sylius.resource.payment.cannot_be_completed')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->checkStockAvailability($event);
    }

    function it_prevents_completing_the_payment_if_reserved_stock_is_not_sufficient(
        OrderItemAvailabilityCheckerInterface $orderItemAvailabilityChecker,
        ResourceControllerEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $event->getSubject()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItemAvailabilityChecker->isReservedStockSufficient($orderItem)->willReturn(false);

        $orderItem->getVariant()->willReturn($variant);
        $variant->getCode()->willReturn('CODE');

        $event->setMessageType('error')->shouldBeCalled();
        $event->setMessage('sylius.resource.payment.cannot_be_completed')->shouldBeCalled();
        $event->setMessageParameters(['%productVariantCode%' => 'CODE'])->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->checkStockAvailability($event);
    }

    function it_does_nothing_if_no_item_is_tracked_when_order_item_availability_checker_is_not_passed(
        AvailabilityCheckerInterface $availabilityChecker,
        ResourceControllerEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith($availabilityChecker);

        $event->getSubject()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(false);

        $event->setMessageType('error')->shouldNotBeCalled();
        $event->setMessage('sylius.resource.payment.cannot_be_completed')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->checkStockAvailability($event);
    }

    function it_does_nothing_if_stock_is_sufficient_for_items_when_order_item_availability_checker_is_not_passed(
        AvailabilityCheckerInterface $availabilityChecker,
        ResourceControllerEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith($availabilityChecker);

        $event->getSubject()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(true);
        $variant->getOnHold()->willReturn(2);
        $variant->getOnHand()->willReturn(3);

        $event->setMessageType('error')->shouldNotBeCalled();
        $event->setMessage('sylius.resource.payment.cannot_be_completed')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->checkStockAvailability($event);
    }

    function it_prevents_completing_the_payment_if_on_hold_amount_is_not_sufficient_for_item_when_order_item_availability_checker_is_not_passed(
        AvailabilityCheckerInterface $availabilityChecker,
        ResourceControllerEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith($availabilityChecker);

        $event->getSubject()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(true);
        $variant->getOnHold()->willReturn(1);
        $variant->getOnHand()->willReturn(3);
        $variant->getCode()->willReturn('CODE');

        $event->setMessageType('error')->shouldBeCalled();
        $event->setMessage('sylius.resource.payment.cannot_be_completed')->shouldBeCalled();
        $event->setMessageParameters(['%productVariantCode%' => 'CODE'])->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->checkStockAvailability($event);
    }

    function it_prevents_completing_the_payment_if_on_hand_amount_is_not_sufficient_for_item_when_order_item_availability_checker_is_not_passed(
        AvailabilityCheckerInterface $availabilityChecker,
        ResourceControllerEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith($availabilityChecker);

        $event->getSubject()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(2);

        $variant->isTracked()->willReturn(true);
        $variant->getOnHold()->willReturn(3);
        $variant->getOnHand()->willReturn(1);
        $variant->getCode()->willReturn('CODE');

        $event->setMessageType('error')->shouldBeCalled();
        $event->setMessage('sylius.resource.payment.cannot_be_completed')->shouldBeCalled();
        $event->setMessageParameters(['%productVariantCode%' => 'CODE'])->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->checkStockAvailability($event);
    }
}
