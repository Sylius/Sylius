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

namespace Tests\Sylius\Component\Core\Payment\Remover;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemover;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

final class OrderPaymentsRemoverTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private OrderPaymentsRemover $remover;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->remover = new OrderPaymentsRemover();
    }

    public function testShouldImplementOrderPaymentsRemoverInterface(): void
    {
        $this->assertInstanceOf(OrderPaymentsRemoverInterface::class, $this->remover);
    }

    public function testShouldRemovePaymentsOfFreeOrder(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(0);

        $this->assertTrue($this->remover->canRemovePayments($this->order));
    }

    public function testShouldNotRemovePaymentsOfNotFreeOrders(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1);

        $this->assertFalse($this->remover->canRemovePayments($this->order));
    }

    public function testShouldDoNothingWhenOrderHasNoPaymentsit_does_nothing_when_order_has_no_payments(): void
    {
        $this->order->expects($this->once())->method('getPayments')->willReturn(new ArrayCollection());
        $this->order->expects($this->never())->method('removePayment')->with($this->anything());

        $this->remover->removePayments($this->order);
    }

    public function testShouldRemoveOnlyPaymentsWithStateCart(): void
    {
        $cartPayment = $this->createMock(PaymentInterface::class);
        $authorizedPayment = $this->createMock(PaymentInterface::class);
        $newPayment = $this->createMock(PaymentInterface::class);
        $processingPayment = $this->createMock(PaymentInterface::class);
        $completedPayment = $this->createMock(PaymentInterface::class);
        $failedPayment = $this->createMock(PaymentInterface::class);
        $cancelledPayment = $this->createMock(PaymentInterface::class);
        $refundedPayment = $this->createMock(PaymentInterface::class);
        $unknownPayment = $this->createMock(PaymentInterface::class);
        $cartPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CART);
        $authorizedPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_AUTHORIZED);
        $newPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $processingPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_PROCESSING);
        $completedPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $failedPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_FAILED);
        $cancelledPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        $refundedPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_REFUNDED);
        $unknownPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_UNKNOWN);
        $this->order->expects($this->once())->method('getPayments')->willReturn(new ArrayCollection([
            $cartPayment,
            $authorizedPayment,
            $newPayment,
            $processingPayment,
            $completedPayment,
            $failedPayment,
            $cancelledPayment,
            $refundedPayment,
            $unknownPayment,
        ]));
        $this->order->expects($this->once())->method('removePayment')->with($cartPayment);

        $this->remover->removePayments($this->order);
    }
}
