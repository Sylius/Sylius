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
use Sylius\Bundle\CoreBundle\EventListener\OrderPaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin OrderPaymentListener
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
final class OrderPaymentListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentListener::class);
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
        $order->getCurrencyCode()->willReturn('USD');

        $payments->last()->willReturn($payment);

        $payment->setAmount(1000)->shouldBeCalled();
        $payment->setCurrencyCode('USD')->shouldBeCalled();

        $this->updateOrderPayment($event);
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('updateOrderPayment', [$event])
        ;
    }

    function it_throws_exception_if_order_has_no_payment(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $order->hasPayments()->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('updateOrderPayment', [$event])
        ;
    }
}
