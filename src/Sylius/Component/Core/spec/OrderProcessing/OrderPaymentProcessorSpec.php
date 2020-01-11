<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPaymentProcessorSpec extends ObjectBehavior
{
    function let(OrderPaymentProviderInterface $orderPaymentProvider): void
    {
        $this->beConstructedWith($orderPaymentProvider, PaymentInterface::STATE_CART);
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

    function it_removes_cart_payments_from_order_if_its_total_is_zero(
        OrderInterface $order,
        PaymentInterface $cartPayment,
        PaymentInterface $cancelledPayment,
        Collection $payments
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getTotal()->willReturn(0);

        $cartPayment->getState()->willReturn(OrderPaymentStates::STATE_CART);
        $cancelledPayment->getState()->willReturn(OrderPaymentStates::STATE_CANCELLED);

        $payments->filter(Argument::type(\Closure::class))->willReturn([$cartPayment]);

        $order->getPayments()->willReturn($payments);
        $order->removePayment($cartPayment)->shouldBeCalledTimes(1);
        $order->removePayment($cancelledPayment)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_is_cancelled(OrderInterface $order): void
    {
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);
        $order->getLastPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }

    function it_sets_last_order_currency_with_target_state_currency_code_and_amount(
        OrderInterface $order,
        PaymentInterface $payment
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn($payment);

        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(1000);

        $payment->setCurrencyCode('PLN')->shouldBeCalled();
        $payment->setAmount(1000)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_provided_order_payment_if_it_is_not_null(
        OrderInterface $order,
        OrderPaymentProviderInterface $orderPaymentProvider,
        PaymentInterface $payment
    ): void {
        $order->getTotal()->willReturn(10);
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentProvider->provideOrderPayment($order, PaymentInterface::STATE_CART)->willReturn($payment);
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_set_order_payment_if_it_cannot_be_provided(
        OrderInterface $order,
        OrderPaymentProviderInterface $orderPaymentProvider
    ): void {
        $order->getTotal()->willReturn(10);
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentProvider
            ->provideOrderPayment($order, PaymentInterface::STATE_CART)
            ->willThrow(NotProvidedOrderPaymentException::class)
        ;
        $order->addPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }
}
