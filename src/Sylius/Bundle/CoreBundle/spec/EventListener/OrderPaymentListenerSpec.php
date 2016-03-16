<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderPaymentListenerSpec extends ObjectBehavior
{
    function let(PaymentProcessorInterface $processor)
    {
        $this->beConstructedWith($processor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderPaymentListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringCreateOrderPayment($event)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUpdateOrderPayment($event)
        ;
    }

    function it_creates_payment(GenericEvent $event, OrderInterface $order, PaymentProcessorInterface $processor)
    {
        $event->getSubject()->willReturn($order);

        $order->getLastPayment()->willReturn(false);

        $processor->processOrderPayments($order)->shouldBeCalled();

        $this->createOrderPayment($event);
    }

    function it_throws_exception_if_order_has_no_payment(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $order->hasPayments()->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUpdateOrderPayment($event)
        ;
    }

    function it_updates_payment(
        GenericEvent $event,
        OrderInterface $order,
        ArrayCollection $payments,
        PaymentInterface $payment
    ) {
        $event->getSubject()->willReturn($order);

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(1000);
        $order->getCurrency()->willReturn('USD');

        $payments->last()->willReturn($payment);

        $payment->setAmount(1000)->shouldBeCalled();
        $payment->setCurrency('USD')->shouldBeCalled();

        $this->updateOrderPayment($event);
    }
}
