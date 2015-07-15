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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentProcessorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $paymentRepository, ObjectManager $paymentManager)
    {
        $this->beConstructedWith($paymentRepository, $paymentManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\PaymentProcessor');
    }

    function it_implements_payment_processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface');
    }

    function it_creates_payment(
        $paymentRepository,
        OrderInterface $order,
        PaymentInterface $payment
    ) {
        $order->getPayments()->willReturn(array())->shouldBeCalled();

        $order->getCurrency()->willReturn('EUR')->shouldBeCalled();
        $order->getTotal()->willReturn(100)->shouldBeCalled();

        $paymentRepository->createNew()->willReturn($payment)->shouldBeCalled();
        $payment->setCurrency('EUR')->shouldBeCalled();
        $payment->setAmount(100)->shouldBeCalled();

        $order->addPayment($payment)->shouldBeCalled();

        $this->createPayment($order)->shouldReturn($payment);
    }

    function it_sets_not_started_payments_as_cancelled_while_creating_payment(
        $paymentManager,
        $paymentRepository,
        OrderInterface $order,
        PaymentInterface $existingPayment,
        PaymentInterface $payment
    ) {
        $existingPayment->getState()->willReturn('new');
        $order->getPayments()->willReturn(array($existingPayment))->shouldBeCalled();

        $existingPayment->setState('cancelled')->shouldBeCalled();
        $paymentManager->flush()->shouldBeCalled();

        $order->getCurrency()->willReturn('EUR')->shouldBeCalled();
        $order->getTotal()->willReturn(100)->shouldBeCalled();

        $paymentRepository->createNew()->willReturn($payment)->shouldBeCalled();
        $payment->setCurrency('EUR')->shouldBeCalled();
        $payment->setAmount(100)->shouldBeCalled();

        $order->addPayment($payment)->shouldBeCalled();

        $this->createPayment($order)->shouldReturn($payment);
    }
}
