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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class PaymentProcessorSpec extends ObjectBehavior
{
    function let(PaymentFactoryInterface $paymentFactory)
    {
        $this->beConstructedWith($paymentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\PaymentProcessor');
    }

    function it_implements_payment_processor_interface()
    {
        $this->shouldImplement(PaymentProcessorInterface::class);
    }

    function it_processes_payment_for_given_order(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $order->getTotal()->willReturn(1234);
        $order->getCurrency()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(false);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(false);

        $paymentFactory->createWithAmountAndCurrency(1234, 'EUR')->willReturn($payment);

        $order->addPayment($payment)->shouldBeCalled();

        $this->processOrderPayments($order);
    }

    function it_sets_payment_method_from_last_cancelled_payment_during_processing(
        $paymentFactory,
        PaymentInterface $payment,
        PaymentInterface $cancelledPayment,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethodFromLastCancelledPayment
    ) {
        $order->getTotal()->willReturn(1234);
        $order->getCurrency()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn($cancelledPayment);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(false);
        $cancelledPayment->getMethod()->willReturn($paymentMethodFromLastCancelledPayment);

        $paymentFactory->createWithAmountAndCurrency(1234, 'EUR')->willReturn($payment);
        $payment->setMethod($paymentMethodFromLastCancelledPayment)->shouldBeCalled();

        $order->addPayment($payment)->shouldBeCalled();

        $this->processOrderPayments($order);
    }

    function it_does_not_set_payment_method_from_last_cancelled_payment_during_processing(
        $paymentFactory,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $order->getTotal()->willReturn(1234);
        $order->getCurrency()->willReturn('EUR');
        $paymentFactory->createWithAmountAndCurrency(1234, 'EUR')->willReturn($payment);
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(false);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn(false);
        $payment->setMethod()->shouldNotBeCalled();
        $order->addPayment($payment)->shouldBeCalled();

        $this->processOrderPayments($order);
    }

    function it_does_not_set_payment_method_from_last_cancelled_payment_during_processing_if_new_payment_exists(
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $newPaymentReadyToPay,
        PaymentInterface $cancelledPayment,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $order->getTotal()->willReturn(1234);
        $order->getCurrency()->willReturn('EUR');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn($cancelledPayment);
        $order->getLastPayment(PaymentInterface::STATE_NEW)->willReturn($newPaymentReadyToPay);

        $paymentFactory->createWithAmountAndCurrency(1234, 'EUR')->willReturn($payment);
        $payment->setMethod($cancelledPayment)->shouldNotBeCalled();
        $order->addPayment($payment)->shouldBeCalled();

        $this->processOrderPayments($order);
    }
}
