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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\PaymentProcessorInterface;
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderPaymentListenerSpec extends ObjectBehavior
{
    function let(PaymentProcessorInterface $processor, EntityRepository $repository, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($processor, $repository, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderPaymentListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCreateOrderPayment($event)
        ;

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringUpdateOrderPayment($event)
        ;
    }

    function it_creates_payment(GenericEvent $event, OrderInterface $order, PaymentProcessorInterface $processor)
    {
        $event->getSubject()->willReturn($order);

        $processor->createPayment($order)->shouldBeCalled();

        $this->createOrderPayment($event);
    }

    function it_throws_exception_if_order_has_no_payment(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $order->getPayment()->willReturn(null);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringUpdateOrderPayment($event)
        ;
    }

    function it_updates_payment(GenericEvent $event, OrderInterface $order, PaymentInterface $payment)
    {
        $event->getSubject()->willReturn($order);

        $order->getPayment()->willReturn($payment);
        $order->getTotal()->willReturn(1000);
        $order->getCurrency()->willReturn('USD');

        $payment->setAmount(1000)->shouldBeCalled();
        $payment->setCurrency('USD')->shouldBeCalled();

        $this->updateOrderPayment($event);
    }

    function it_throws_exception_if_event_has_non_payment_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringUpdateOrderOnPayment($event)
        ;
    }

    function it_dispatches_event_on_payment_if_complete(
        GenericEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        EntityRepository $repository,
        EventDispatcherInterface $dispatcher
    )
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $event->getSubject()->willReturn($payment);
        $event->getArguments()->willReturn(array('foo' => 'bar'));

        $repository->findOneBy(array('payment' => $payment))->willReturn($order);

        $dispatcher->dispatch(SyliusOrderEvents::PRE_PAY, Argument::type('Symfony\Component\EventDispatcher\GenericEvent'))->shouldBeCalled();
        $dispatcher->dispatch(SyliusOrderEvents::POST_PAY, Argument::type('Symfony\Component\EventDispatcher\GenericEvent'))->shouldBeCalled();

        $this->updateOrderOnPayment($event);
    }

    function it_does_not_dispatch_event_if_payment_is_not_complete(
        GenericEvent $event,
        PaymentInterface $payment,
        OrderInterface $order,
        EntityRepository $repository,
        EventDispatcherInterface $dispatcher
    )
    {
        $payment->getState()->willReturn('anything_but_completed');

        $event->getSubject()->willReturn($payment);
        $event->getArguments()->willReturn(array('foo' => 'bar'));

        $repository->findOneBy(array('payment' => $payment))->willReturn($order);

        $dispatcher->dispatch(SyliusOrderEvents::PRE_PAY, Argument::type('Symfony\Component\EventDispatcher\GenericEvent'))->shouldNotBeCalled();
        $dispatcher->dispatch(SyliusOrderEvents::POST_PAY, Argument::type('Symfony\Component\EventDispatcher\GenericEvent'))->shouldNotBeCalled();

        $this->updateOrderOnPayment($event);
    }
}
