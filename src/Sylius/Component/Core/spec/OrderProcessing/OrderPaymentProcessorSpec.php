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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @mixin OrderPaymentProcessor
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderPaymentProcessorSpec extends ObjectBehavior
{
    function let(PaymentFactoryInterface $paymentFactory)
    {
        $this->beConstructedWith($paymentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentProcessor::class);
    }

    function it_is_an_order_processor()
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_processes_payment_for_given_order(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(null);

        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);

        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_payment_method_from_last_cancelled_payment_during_processing(
        $paymentFactory,
        PaymentInterface $payment,
        PaymentInterface $cancelledPayment,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethodFromLastCancelledPayment
    ) {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn($cancelledPayment);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(null);
        $cancelledPayment->getMethod()->willReturn($paymentMethodFromLastCancelledPayment);

        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);
        $payment->setMethod($paymentMethodFromLastCancelledPayment)->shouldBeCalled();

        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_set_payment_method_from_last_cancelled_payment_during_processing(
        $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(null);
        $payment->setMethod()->shouldNotBeCalled();
        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_sets_payment_method_from_last_failed_payment_during_processing(
        $paymentFactory,
        PaymentInterface $payment,
        PaymentInterface $failedPayment,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethodFromLastFailedPayment
    ) {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn($failedPayment);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(null);
        $failedPayment->getMethod()->willReturn($paymentMethodFromLastFailedPayment);

        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);
        $payment->setMethod($paymentMethodFromLastFailedPayment)->shouldBeCalled();

        $order->addPayment($payment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_set_payment_method_from_last_failed_payment_during_processing(
        $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(null);
        $payment->setMethod()->shouldNotBeCalled();
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
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(1234);
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn($cancelledPayment);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn($newPaymentReadyToPay);

        $paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR')->willReturn($payment);
        $payment->setMethod($cancelledPayment)->shouldNotBeCalled();
        $order->addPayment($payment)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_sets_orders_total_on_payment_amount_when_payment_is_new(OrderInterface $order, PaymentInterface $payment)
    {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getTotal()->willReturn(123);
        $order->getCurrencyCode()->willReturn('EUR');

        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn($payment);

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
