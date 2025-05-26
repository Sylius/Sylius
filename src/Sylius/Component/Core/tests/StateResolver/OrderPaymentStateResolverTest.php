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

namespace Tests\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\StateResolver\OrderPaymentStateResolver;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderPaymentStateResolverTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&OrderInterface $order;

    private MockObject&PaymentInterface $firstPayment;

    private MockObject&PaymentInterface $secondPayment;

    private OrderPaymentStateResolver $stateResolver;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstPayment = $this->createMock(PaymentInterface::class);
        $this->secondPayment = $this->createMock(PaymentInterface::class);
        $this->stateResolver = new OrderPaymentStateResolver($this->stateMachine);
    }

    public function testShouldImplementStateResolverInterface(): void
    {
        $this->assertInstanceOf(StateResolverInterface::class, $this->stateResolver);
    }

    public function testShouldMarkOrderAsRefundedIfAllITsPaymentsAreRefunded(): void
    {
        $this->firstPayment->expects($this->once())->method('getAmount')->willReturn(6000);
        $this->firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_REFUNDED);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(4000);
        $this->secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_REFUNDED);
        $this->order
            ->expects($this->once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->once())->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsRefundedIfItsPaymentsAreRefundedOrFailedButAtLeastOneIsRefunded(): void
    {
        $this->firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_FAILED);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(10000);
        $this->secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_REFUNDED);
        $this->order
            ->expects($this->once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->once())->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsPaidIfFullyPaid(): void
    {
        $this->firstPayment->expects($this->once())->method('getAmount')->willReturn(10000);
        $this->firstPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->order
            ->expects($this->exactly(2))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment]));
        $this->order->expects($this->exactly(2))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsPaidIfItDoesNotHaveAnyPayments(): void
    {
        $this->order
            ->expects($this->exactly(3))
            ->method('getPayments')
            ->willReturn(new ArrayCollection());
        $this->order->expects($this->once())->method('getTotal')->willReturn(0);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsPaidIfFullyPaidEvenIfPreviousPaymentWasFailed(): void
    {
        $this->firstPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_FAILED);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(10000);
        $this->secondPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->order
            ->expects($this->exactly(2))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->exactly(2))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsPartiallyRefundedIfOneOfThePaymentIsRefunded(): void
    {
        $this->firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(4000);
        $this->secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_REFUNDED);
        $this->order
            ->expects($this->once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->exactly(2))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsCompletedIfFullyPaidMultiplePayments(): void
    {
        $this->firstPayment->expects($this->once())->method('getAmount')->willReturn(6000);
        $this->firstPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(4000);
        $this->secondPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->order
            ->expects($this->exactly(2))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->exactly(2))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsPartiallyPaidIfOneOfThePaymentIsProcessing(): void
    {
        $this->firstPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_PROCESSING);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(4000);
        $this->secondPayment->expects($this->exactly(2))->method('getState')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->order
            ->expects($this->exactly(3))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->exactly(3))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsAuthorizedIfAllItsPaymentsAreAuthorized(): void
    {
        $this->firstPayment->expects($this->once())->method('getAmount')->willReturn(6000);
        $this->firstPayment->expects($this->exactly(3))->method('getState')->willReturn(PaymentInterface::STATE_AUTHORIZED);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(4000);
        $this->secondPayment->expects($this->exactly(3))->method('getState')->willReturn(PaymentInterface::STATE_AUTHORIZED);
        $this->order
            ->expects($this->exactly(4))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->exactly(3))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_AUTHORIZE)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_AUTHORIZE);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarksOrderAsPartiallyAuthorizedIfOneOfThePaymentsIsProcessingAndOneOfThePaymentsIsAuthorized(): void
    {
        $this->firstPayment->expects($this->exactly(3))->method('getState')->willReturn(PaymentInterface::STATE_PROCESSING);
        $this->secondPayment->expects($this->once())->method('getAmount')->willReturn(4000);
        $this->secondPayment->expects($this->exactly(3))->method('getState')->willReturn(PaymentInterface::STATE_AUTHORIZED);
        $this->order
            ->expects($this->exactly(4))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment, $this->secondPayment]));
        $this->order->expects($this->exactly(4))->method('getTotal')->willReturn(10000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_AUTHORIZE)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_AUTHORIZE);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsAwaitingPaymentIfPaymentsIsProcessing(): void
    {
        $this->firstPayment->expects($this->once())->method('getAmount')->willReturn(6000);
        $this->firstPayment->expects($this->exactly(4))->method('getState')->willReturn(PaymentInterface::STATE_PROCESSING);
        $this->order
            ->expects($this->exactly(5))
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$this->firstPayment]));
        $this->order->expects($this->exactly(4))->method('getTotal')->willReturn(6000);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);

        $this->stateResolver->resolve($this->order);
    }
}
