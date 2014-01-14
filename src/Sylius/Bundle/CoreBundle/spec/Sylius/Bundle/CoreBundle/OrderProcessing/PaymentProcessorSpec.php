<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class PaymentProcessorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_sets_payment_to_orders_without_payment(OrderInterface $order, $repository, PaymentInterface $payment)
    {
        $payment->setCurrency(Argument::any())->willReturn($payment);
        $payment->setAmount(Argument::any())->willReturn($payment);

        $repository->createNew()->shouldBeCalled()->willReturn($payment);

        $order->getPayment()->shouldBeCalled()->willReturn(null);
        $order->getCurrency()->shouldBeCalled();

        $order->getTotal()->shouldBeCalled();
        $order->setPayment($payment)->shouldBeCalled();
        $this->createPayment($order);
    }

    function it_leaves_existing_payment_alone(OrderInterface $order, $repository, PaymentInterface $payment)
    {
        $order->getPayment()->willReturn($payment);

        $repository->createNew()->shouldNotBeCalled();
        $order->setPayment(Argument::any())->shouldNotBeCalled();

        $this->createPayment($order);
    }
}
