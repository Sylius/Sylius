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

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPaymentProcessorSpec extends ObjectBehavior
{
    function let(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentsRemover,
            [
                OrderInterface::STATE_FULFILLED,
            ],
            PaymentInterface::STATE_CART,
        );
    }

    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_throws_exception_if_passed_order_is_not_core_order(BaseOrderInterface $order): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [$order])
        ;
    }

    function it_does_nothing_if_the_order_state_is_in_unsupported_states(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderInterface $order,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);
        $order->getLastPayment(Argument::any())->shouldNotBeCalled();

        $orderPaymentProvider->provideOrderPayment($order)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_removes_cart_payments_from_order_when_using_payments_remover(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(true);
        $orderPaymentsRemover->removePayments($order)->shouldBeCalled();

        $order->addPayment(Argument::any())->shouldNotBeCalled();
        $order->getLastPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }

    function it_sets_last_order_currency_with_target_state_currency_code_and_amount(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn($payment);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(false);

        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(1000);

        $payment->setCurrencyCode('PLN')->shouldBeCalled();
        $payment->setAmount(1000)->shouldBeCalled();

        $orderPaymentProvider->provideOrderPayment($order, PaymentInterface::STATE_CART)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_sets_provided_order_payment_if_it_is_not_null(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(false);

        $orderPaymentProvider->provideOrderPayment($order, PaymentInterface::STATE_CART)->willReturn($payment);
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_set_order_payment_if_it_cannot_be_provided(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(false);

        $orderPaymentProvider
            ->provideOrderPayment($order, PaymentInterface::STATE_CART)
            ->willThrow(NotProvidedOrderPaymentException::class)
        ;
        $order->addPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }
}
