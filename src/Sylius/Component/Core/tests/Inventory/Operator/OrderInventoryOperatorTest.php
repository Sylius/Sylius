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

namespace Tests\Sylius\Component\Core\Inventory\Operator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHandException;
use Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHoldException;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperator;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderPaymentStates;

final class OrderInventoryOperatorTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&ProductVariantInterface $productVariant;

    private OrderInventoryOperator $operator;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->operator = new OrderInventoryOperator();
    }

    public function testShouldImplementOrderInventoryOperatorInterface(): void
    {
        $this->assertInstanceOf(OrderInventoryOperatorInterface::class, $this->operator);
    }

    public function testShouldIncreasesOnHoldQuantityDuringHolding(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->once())->method('setOnHold')->with(10);

        $this->operator->hold($this->order);
    }

    public function testShouldDecreasesOnHoldAndOnHandDuringSelling(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->exactly(4))->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->exactly(2))->method('getOnHold')->willReturn(10);
        $this->productVariant->expects($this->exactly(2))->method('getOnHand')->willReturn(10);
        $this->productVariant->expects($this->once())->method('setOnHold')->with(0);
        $this->productVariant->expects($this->once())->method('setOnHand')->with(0);

        $this->operator->sell($this->order);
    }

    public function testShouldDecreasesOnHoldQuantityDuringCancelling(): void
    {
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->exactly(2))->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->exactly(2))->method('getOnHold')->willReturn(10);
        $this->productVariant->expects($this->once())->method('setOnHold')->with(0);

        $this->operator->cancel($this->order);
    }

    public function testShouldIncreasesOnHandDuringCancellingOfPaidOrder(): void
    {
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_PAID);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->once())->method('getOnHand')->willReturn(0);
        $this->productVariant->expects($this->once())->method('setOnHand')->with(10);

        $this->operator->cancel($this->order);
    }

    public function testShouldIncreasesOnHandDuringCancellingOfRefundedOrder(): void
    {
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_REFUNDED);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->once())->method('getOnHand')->willReturn(0);
        $this->productVariant->expects($this->once())->method('setOnHand')->with(10);

        $this->operator->cancel($this->order);
    }

    public function testShouldThrowNotEnoughUnitsOnHoldExceptionIfDifferenceBetweenOnHoldAndItemQuantityIsSmallerThanZeroDuringCancelling(): void
    {
        $this->expectException(NotEnoughUnitsOnHoldException::class);
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->once())->method('getOnHold')->willReturn(5);
        $this->productVariant->expects($this->once())->method('getName')->willReturn('Red Skirt');

        $this->operator->cancel($this->order);
    }

    public function testShouldThrowNotEnoughUnitsOnHoldExceptionIfDifferenceBetweenOnHoldAndItemQuantityIsSmallerThanZeroDuringSelling(): void
    {
        $this->expectException(NotEnoughUnitsOnHoldException::class);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->once())->method('getOnHold')->willReturn(5);
        $this->productVariant->expects($this->once())->method('getName')->willReturn('Red Skirt');

        $this->operator->sell($this->order);
    }

    public function testShouldThrowNotEnoughUnitsOnHoldExceptionIfDifferenceBetweenOnHandAndItemQuantityIsSmallerThanZeroDuringSelling(): void
    {
        $this->expectException(NotEnoughUnitsOnHandException::class);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->orderItem->expects($this->exactly(2))->method('getQuantity')->willReturn(10);
        $this->productVariant->expects($this->once())->method('getOnHold')->willReturn(10);
        $this->productVariant->expects($this->once())->method('getOnHand')->willReturn(5);
        $this->productVariant->expects($this->once())->method('getName')->willReturn('Red Skirt');

        $this->operator->sell($this->order);
    }

    public function testShouldDoNothingIfVariantIsNotTrackedDuringCancelling(): void
    {
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(false);
        $this->productVariant->expects($this->never())->method('setOnHold')->with($this->anything());

        $this->operator->cancel($this->order);
    }

    public function testShouldDoNothingIfVariantIsNotTrackedAndOrderIsPaidDuringCancelling(): void
    {
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_PAID);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(false);
        $this->productVariant->expects($this->never())->method('setOnHand')->with($this->anything());

        $this->operator->cancel($this->order);
    }

    public function testShouldDoNothingIfVariantIsNotTrackedDuringHolding(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(false);
        $this->productVariant->expects($this->never())->method('setOnHold')->with($this->anything());

        $this->operator->hold($this->order);
    }

    public function testShouldDoNothingIfVariantIsNotTrackedDuringSelling(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(false);
        $this->productVariant->expects($this->never())->method('setOnHold')->with($this->anything());
        $this->productVariant->expects($this->never())->method('setOnHand')->with($this->anything());

        $this->operator->sell($this->order);
    }
}
