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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Refresher\OrderPaymentRefresherInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPaymentProcessorSpec extends ObjectBehavior
{
    function let(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentRefresherInterface $orderPaymentRefresher,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentRefresher,
            PaymentInterface::STATE_CART
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

    function it_does_nothing_if_the_order_is_cancelled_when_no_unsupported_states_were_passed(
        OrderInterface $order,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);
        $order->getLastPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_state_is_in_unsupported_states(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentRefresher,
            PaymentInterface::STATE_CART,
            null, [OrderInterface::STATE_FULFILLED],
        );

        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);
        $order->getLastPayment(Argument::any())->shouldNotBeCalled();

        $orderPaymentProvider->provideOrderPayment($order)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_removes_cart_payments_from_order_if_its_total_is_zero(
        OrderInterface $order,
        PaymentInterface $cartPayment,
        PaymentInterface $cancelledPayment,
        Collection $payments,
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

    function it_sets_last_order_currency_with_target_state_currency_code_and_amount(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn($payment);

        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(1000);

        $payment->setCurrencyCode('PLN')->shouldBeCalled();
        $payment->setAmount(1000)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_provided_order_payment_if_it_is_not_null(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderInterface $order,
        OrderPaymentProviderInterface $orderPaymentProvider,
        PaymentInterface $payment,
    ): void {
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $order->getTotal()->willReturn(10);
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentProvider->provideOrderPayment($order, PaymentInterface::STATE_CART)->willReturn($payment);
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_set_order_payment_if_it_cannot_be_provided(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderInterface $order,
        OrderPaymentProviderInterface $orderPaymentProvider,
    ): void {
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

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

    function it_removes_cart_payments_from_order_when_using_payments_remover(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentRefresher,
            PaymentInterface::STATE_CART,
            $orderPaymentsRemover
        );


        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getTotal()->shouldNotBeCalled();

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(true);
        $orderPaymentsRemover->removePayments($order)->shouldBeCalled();
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $this->process($order);
    }

    function it_sets_last_order_currency_with_target_state_currency_code_and_amount_when_using_payments_remover(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentRefresher,
            PaymentInterface::STATE_CART,
            $orderPaymentsRemover
        );

        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn($payment);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(false);
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(1000);

        $payment->setCurrencyCode('PLN')->shouldBeCalled();
        $payment->setAmount(1000)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_provided_order_payment_if_it_is_not_null_when_using_payments_remover(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentRefresher,
            PaymentInterface::STATE_CART,
            $orderPaymentsRemover
        );

        $order->getTotal()->willReturn(10);
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(false);
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $orderPaymentProvider->provideOrderPayment($order, PaymentInterface::STATE_CART)->willReturn($payment);
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_set_order_payment_if_it_cannot_be_provided_when_using_payments_remover(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentRefresher,
            PaymentInterface::STATE_CART,
            $orderPaymentsRemover
        );

        $order->getTotal()->willReturn(10);
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentsRemover->canRemovePayments($order)->willReturn(false);
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);

        $orderPaymentProvider
            ->provideOrderPayment($order, PaymentInterface::STATE_CART)
            ->willThrow(NotProvidedOrderPaymentException::class)
        ;
        $order->addPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }

    function it_refreshes_payment_when_needed(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderInterface $order,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getTotal()->willReturn(100);

        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(true);
        $orderPaymentRefresher->refreshPayments($order, OrderInterface::STATE_CART)->shouldBeCalledOnce();

        $this->process($order);
    }

    function it_doesnt_refreshe_payment_if_not_needed(
        OrderPaymentRefresherInterface $orderPaymentRefresher,
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $orderPaymentRefresher->isPaymentRefreshingNeeded($order)->willReturn(false);
        $orderPaymentRefresher->refreshPayments($order)->shouldNotBeCalled();

        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $order->getTotal()->willReturn(100);
        $order->getLastPayment(PaymentInterface::STATE_CART)->willReturn(null);

        $orderPaymentProvider->provideOrderPayment($order, PaymentInterface::STATE_CART)->willReturn($payment);
        $order->addPayment($payment)->shouldBeCalledOnce();

        $this->process($order);
    }
}
