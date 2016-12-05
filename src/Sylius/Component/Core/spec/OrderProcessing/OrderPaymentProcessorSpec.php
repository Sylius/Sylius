<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderPaymentProcessorSpec extends ObjectBehavior
{
    function let(
        PaymentFactoryInterface $paymentFactory,
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver
    ) {
        $this->beConstructedWith($paymentFactory, $defaultPaymentMethodResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentProcessor::class);
    }

    function it_is_an_order_processor()
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_sets_default_payment_method_during_processing_if_order_has_not_payment_in_state_cancelled_failed_or_new(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order,
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentMethod $paymentMethod
    ) {
        $payments = new ArrayCollection();
        $order->getPayments()->willReturn($payments);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getLastCartPayment()->willReturn(null);

        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);
        $payment->setOrder($order);

        $defaultPaymentMethodResolver->getDefaultPaymentMethod($payment)->willReturn($paymentMethod);

        $payment->setOrder($order)->shouldBeCalled();
        $payment->setMethod($paymentMethod)->shouldBeCalled();
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_payment_method_from_last_cancelled_payment_during_processing(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        PaymentInterface $cancelledPayment,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethodFromLastCancelledPayment
    ) {
        $payments = new ArrayCollection();
        $payments->add($cancelledPayment->getWrappedObject());
        $order->getPayments()->willReturn($payments);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getLastCartPayment()->willReturn(null);

        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);

        $cancelledPayment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $cancelledPayment->getMethod()->willReturn($paymentMethodFromLastCancelledPayment);

        $payment->setMethod($paymentMethodFromLastCancelledPayment)->shouldBeCalled();
        $payment->setOrder($order)->shouldBeCalled();
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_payment_method_from_last_failed_payment_during_processing(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        PaymentInterface $failedPayment,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethodFromLastFailedPayment
    ) {
        $payments = new ArrayCollection();
        $payments->add($failedPayment->getWrappedObject());
        $order->getPayments()->willReturn($payments);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getLastCartPayment()->willReturn(null);

        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);

        $failedPayment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $failedPayment->getMethod()->willReturn($paymentMethodFromLastFailedPayment);

        $payment->setMethod($paymentMethodFromLastFailedPayment)->shouldBeCalled();
        $payment->setOrder($order)->shouldBeCalled();
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_add_payment_during_processing_if_new_payment_already_exists(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $newPaymentReadyToPay,
        PaymentInterface $cancelledPayment,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $payments = new ArrayCollection();
        $payments->add($cancelledPayment);
        $order->getPayments()->willReturn($payments);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getLastCartPayment()->willReturn($newPaymentReadyToPay);

        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);

        $payment->setMethod($cancelledPayment)->shouldNotBeCalled();
        $order->addPayment($payment)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_not_add_payment_method_to_payment_when_default_method_resolver_throw_an_exception(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order,
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver
    ) {
        $payments = new ArrayCollection();
        $order->getPayments()->willReturn($payments);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getLastCartPayment()->willReturn(null);

        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);

        $defaultPaymentMethodResolver->getDefaultPaymentMethod($payment)->shouldBeCalled();

        $payment->setOrder($order)->shouldBeCalled();
        $payment->setMethod(Argument::any())->shouldNotBeCalled();
        $order->addPayment($payment)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_sets_orders_total_on_payment_amount_when_payment_is_new(
        OrderInterface $order,
        PaymentInterface $payment
    ) {
        $payments = new ArrayCollection();
        $order->getPayments()->willReturn($payments);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(123);
        $order->getCurrencyCode()->willReturn('EUR');

        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $order->getLastCartPayment()->willReturn($payment);

        $payment->setAmount(123)->shouldBeCalled();
        $payment->setCurrencyCode('EUR')->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_add_a_new_payment_if_the_order_is_cancelled(OrderInterface $order)
    {
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);
        $order->addPayment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }
}
