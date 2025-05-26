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

namespace Tests\Sylius\Component\Core\OrderProcessing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPaymentProcessorTest extends TestCase
{
    private MockObject&OrderPaymentProviderInterface $orderPaymentProvider;

    private MockObject&OrderPaymentsRemoverInterface $orderPaymentsRemover;

    private MockObject&OrderInterface $order;

    private MockObject&PaymentInterface $payment;

    private OrderPaymentProcessor $orderPaymentProcessor;

    protected function setUp(): void
    {
        $this->orderPaymentProvider = $this->createMock(OrderPaymentProviderInterface::class);
        $this->orderPaymentsRemover = $this->createMock(OrderPaymentsRemoverInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->orderPaymentProcessor = new OrderPaymentProcessor(
            $this->orderPaymentProvider,
            $this->orderPaymentsRemover,
            [OrderInterface::STATE_FULFILLED],
            PaymentInterface::STATE_CART,
        );
    }

    public function testShouldImplementOrderProcessor(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderPaymentProcessor);
    }

    public function testShouldThrowExceptionIfPassedOrderIsNotCoreOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->orderPaymentProcessor->process($this->createMock(BaseOrderInterface::class));
    }

    public function testShouldDoNothingIfOrderStateIsInUnsupportedStates(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn(OrderInterface::STATE_FULFILLED);
        $this->order->expects($this->never())->method('getLastPayment')->with($this->anything());
        $this->orderPaymentProvider->expects($this->never())->method('provideOrderPayment')->with($this->order);

        $this->orderPaymentProcessor->process($this->order);
    }

    public function testShouldRemoveCartPaymentsFromOrderWhenUsingPaymentsRemover(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->orderPaymentsRemover->expects($this->once())->method('canRemovePayments')->with($this->order)->willReturn(true);
        $this->orderPaymentsRemover->expects($this->once())->method('removePayments')->with($this->order);
        $this->order->expects($this->never())->method('addPayment')->with($this->anything());
        $this->order->expects($this->never())->method('getLastPayment')->with($this->anything());

        $this->orderPaymentProcessor->process($this->order);
    }

    public function testShouldSetsLastOrderCurrencyWithTargetStateCurrencyCodeAndAmount(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->order->expects($this->once())->method('getLastPayment')->with(PaymentInterface::STATE_CART)->willReturn($this->payment);
        $this->orderPaymentsRemover
            ->expects($this->once())
            ->method('canRemovePayments')
            ->with($this->order)
            ->willReturn(false);
        $this->order->expects($this->once())->method('getCurrencyCode')->willReturn('PLN');
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->payment->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $this->payment->expects($this->once())->method('setAmount')->with(1000);
        $this->orderPaymentProvider
            ->expects($this->never())
            ->method('provideOrderPayment')
            ->with($this->order, PaymentInterface::STATE_CART);

        $this->orderPaymentProcessor->process($this->order);
    }

    public function testShouldSetsProvidedOrderPaymentIfItIsNotNull(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->order->expects($this->once())->method('getLastPayment')->with(PaymentInterface::STATE_CART)->willReturn(null);
        $this->orderPaymentsRemover
            ->expects($this->once())
            ->method('canRemovePayments')
            ->with($this->order)
            ->willReturn(false);
        $this->orderPaymentProvider
            ->expects($this->once())
            ->method('provideOrderPayment')
            ->with($this->order, PaymentInterface::STATE_CART)
            ->willReturn($this->payment);
        $this->order->expects($this->once())->method('addPayment')->with($this->payment);

        $this->orderPaymentProcessor->process($this->order);
    }

    public function testShouldNotSetOrderPaymentIfItCannotBeProvided(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->order->expects($this->once())->method('getLastPayment')->with(PaymentInterface::STATE_CART)->willReturn(null);
        $this->orderPaymentsRemover
            ->expects($this->once())
            ->method('canRemovePayments')
            ->with($this->order)
            ->willReturn(false);
        $this->orderPaymentProvider
            ->expects($this->once())
            ->method('provideOrderPayment')
            ->with($this->order, PaymentInterface::STATE_CART)
            ->willThrowException(new NotProvidedOrderPaymentException());
        $this->order->expects($this->never())->method('addPayment')->with($this->anything());

        $this->orderPaymentProcessor->process($this->order);
    }
}
