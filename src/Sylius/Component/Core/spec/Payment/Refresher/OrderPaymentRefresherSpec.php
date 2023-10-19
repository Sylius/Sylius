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

namespace spec\Sylius\Component\Core\Payment\Refresher;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

final class OrderPaymentRefresherSpec extends ObjectBehavior
{
    function let(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
    ): void {
        $this->beConstructedWith(
            $orderPaymentProvider,
            $orderPaymentsRemover,
        );
    }

    function it_should_be_refreshed_when_one_payment_has_disabled_method(
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentInterface $payment1,
        PaymentInterface $payment2,
        PaymentMethodInterface $paymentMethod1,
        PaymentMethodInterface $paymentMethod2,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->isSkippingPaymentStepAllowed()->willReturn(true);

        $order->getPayments()->willReturn(new ArrayCollection([
            $payment1->getWrappedObject(),
            $payment2->getWrappedObject(),
        ]));

        $payment1->getMethod()->willReturn($paymentMethod1);
        $payment2->getMethod()->willReturn($paymentMethod2);
        $paymentMethod1->isEnabled()->willReturn(true);
        $paymentMethod1->isEnabled()->willReturn(false);

        $this->isPaymentRefreshingNeeded($order)->shouldReturn(true);
    }

    function it_should_not_be_refreshed_when_no_payment_has_disabled_method(
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentInterface $payment1,
        PaymentInterface $payment2,
        PaymentMethodInterface $paymentMethod1,
        PaymentMethodInterface $paymentMethod2,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->isSkippingPaymentStepAllowed()->willReturn(true);

        $order->getPayments()->willReturn(new ArrayCollection([
            $payment1->getWrappedObject(),
            $payment2->getWrappedObject(),
        ]));

        $payment1->getMethod()->willReturn($paymentMethod1);
        $payment2->getMethod()->willReturn($paymentMethod2);
        $paymentMethod1->isEnabled()->willReturn(true);
        $paymentMethod1->isEnabled()->willReturn(true);

        $this->isPaymentRefreshingNeeded($order)->shouldReturn(false);
    }

    function it_should_not_be_refreshed_when_channel_has_no_skipping_payment_step_allowed(
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentInterface $payment1,
        PaymentInterface $payment2,
        PaymentMethodInterface $paymentMethod1,
        PaymentMethodInterface $paymentMethod2,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->isSkippingPaymentStepAllowed()->willReturn(false);

        $order->getPayments()->willReturn(new ArrayCollection([
            $payment1->getWrappedObject(),
            $payment2->getWrappedObject(),
        ]));

        $payment1->getMethod()->willReturn($paymentMethod1);
        $payment2->getMethod()->willReturn($paymentMethod2);
        $paymentMethod1->isEnabled()->willReturn(true);
        $paymentMethod1->isEnabled()->willReturn(false);

        $this->isPaymentRefreshingNeeded($order)->shouldReturn(false);
    }

    function it_removes_previous_and_creates_new_payment_method(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
    ): void {
        $orderPaymentsRemover->removePayments($order)->shouldBeCalledOnce();
        $orderPaymentProvider->provideOrderPayment($order, "cart")->shouldBeCalledOnce();

        $this->refreshPayments($order, "cart");
    }

    function it_ignores_no_payment_exception_when_remove_previous_and_creates_new_payment_method(
        OrderPaymentProviderInterface $orderPaymentProvider,
        OrderPaymentsRemoverInterface $orderPaymentsRemover,
        OrderInterface $order,
    ): void {
        $orderPaymentsRemover->removePayments($order)->shouldBeCalledOnce();
        $orderPaymentProvider->provideOrderPayment($order, "cart")->willThrow(NotProvidedOrderPaymentException::class);

        $this->refreshPayments($order, "cart");
    }
}
