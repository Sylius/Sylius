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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class PaymentPreCompleteListenerTest extends TestCase
{
    private MockObject&OrderItemAvailabilityCheckerInterface $orderItemAvailabilityChecker;

    private PaymentPreCompleteListener $listener;

    protected function setUp(): void
    {
        $this->orderItemAvailabilityChecker = $this->createMock(OrderItemAvailabilityCheckerInterface::class);
        $this->listener = new PaymentPreCompleteListener($this->orderItemAvailabilityChecker);
    }

    public function testDoesNothingIfReservedStockIsSufficient(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);

        $event->method('getSubject')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $this->orderItemAvailabilityChecker->method('isReservedStockSufficient')->with($orderItem)->willReturn(true);

        $event->expects($this->never())->method('setMessageType');
        $event->expects($this->never())->method('setMessage');
        $event->expects($this->never())->method('stopPropagation');

        $this->listener->checkStockAvailability($event);
    }

    public function testPreventsCompletingPaymentIfReservedStockIsNotSufficient(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->method('getSubject')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $this->orderItemAvailabilityChecker->method('isReservedStockSufficient')->with($orderItem)->willReturn(false);
        $orderItem->method('getVariant')->willReturn($variant);
        $variant->method('getCode')->willReturn('CODE');

        $event->expects($this->once())->method('setMessageType')->with('error');
        $event->expects($this->once())->method('setMessage')->with('sylius.resource.payment.cannot_be_completed');
        $event->expects($this->once())->method('setMessageParameters')->with(['%productVariantCode%' => 'CODE']);
        $event->expects($this->once())->method('stopPropagation');

        $this->listener->checkStockAvailability($event);
    }

    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    public function testDoesNothingIfNoItemIsTrackedWhenAvailabilityCheckerIsUsed(): void
    {
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->listener = new PaymentPreCompleteListener($this->availabilityChecker);

        $event = $this->createMock(GenericEvent::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->method('getSubject')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getQuantity')->willReturn(2);
        $variant->method('isTracked')->willReturn(false);

        $event->expects($this->never())->method('setMessageType');
        $event->expects($this->never())->method('setMessage');
        $event->expects($this->never())->method('stopPropagation');

        $this->listener->checkStockAvailability($event);
    }

    public function testDoesNothingIfStockIsSufficientWhenAvailabilityCheckerIsUsed(): void
    {
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->listener = new PaymentPreCompleteListener($this->availabilityChecker);

        $event = $this->createMock(GenericEvent::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->method('getSubject')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getQuantity')->willReturn(2);
        $variant->method('isTracked')->willReturn(true);
        $variant->method('getOnHold')->willReturn(2);
        $variant->method('getOnHand')->willReturn(3);

        $event->expects($this->never())->method('setMessageType');
        $event->expects($this->never())->method('setMessage');
        $event->expects($this->never())->method('stopPropagation');

        $this->listener->checkStockAvailability($event);
    }

    public function testPreventsCompletingPaymentIfOnHoldAmountIsNotSufficientWhenAvailabilityCheckerIsUsed(): void
    {
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->listener = new PaymentPreCompleteListener($this->availabilityChecker);

        $event = $this->createMock(GenericEvent::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->method('getSubject')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getQuantity')->willReturn(2);
        $variant->method('isTracked')->willReturn(true);
        $variant->method('getOnHold')->willReturn(1);
        $variant->method('getOnHand')->willReturn(3);
        $variant->method('getCode')->willReturn('CODE');

        $event->expects($this->once())->method('setMessageType')->with('error');
        $event->expects($this->once())->method('setMessage')->with('sylius.resource.payment.cannot_be_completed');
        $event->expects($this->once())->method('setMessageParameters')->with(['%productVariantCode%' => 'CODE']);
        $event->expects($this->once())->method('stopPropagation');

        $this->listener->checkStockAvailability($event);
    }

    public function testPreventsCompletingPaymentIfOnHandAmountIsNotSufficientForItemWhenOrderItemAvailabilityCheckerIsNotPassed(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->method('getSubject')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getQuantity')->willReturn(2);
        $variant->method('isTracked')->willReturn(true);
        $variant->method('getOnHold')->willReturn(3);
        $variant->method('getOnHand')->willReturn(1);
        $variant->method('getCode')->willReturn('CODE');

        $event->expects($this->once())->method('setMessageType')->with('error');
        $event->expects($this->once())->method('setMessage')->with('sylius.resource.payment.cannot_be_completed');
        $event->expects($this->once())->method('setMessageParameters')->with(['%productVariantCode%' => 'CODE']);
        $event->expects($this->once())->method('stopPropagation');

        $this->listener->checkStockAvailability($event);
    }
}
