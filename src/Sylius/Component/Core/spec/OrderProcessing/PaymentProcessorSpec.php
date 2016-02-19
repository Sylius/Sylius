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
use Sylius\Component\Payment\Model\PaymentInterface;

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

        $paymentFactory->createWithAmountAndCurrency(1234, 'EUR')->willReturn($payment);

        $order->addPayment($payment)->shouldBeCalled();

        $this->processOrderPayments($order);
    }
}
