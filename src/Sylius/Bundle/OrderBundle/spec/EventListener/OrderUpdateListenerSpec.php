<?php

namespace spec\Sylius\Bundle\OrderBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderUpdateListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\OrderUpdateListener');
    }

    function it_reculate_order_total(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->shouldBeCalled()->willReturn($order);
        $order->calculateTotal()->shouldBeCalled();

        $this->recalculateOrderTotal($event);
    }
}
