<?php

namespace spec\Sylius\Bundle\OrderBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderUpdateListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\OrderUpdateListener');
    }

    public function it_reculate_order_total(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->shouldBeCalled()->willReturn($order);
        $order->calculateTotal()->shouldBeCalled();

        $this->recalculateOrderTotal($event);
    }
}
