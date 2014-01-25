<?php

namespace spec\Sylius\Bundle\OrderBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CompleteOrderListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\CompleteOrderListener');
    }

    function it_throws_exception_if_invalid_argument(GenericEvent $event, \stdClass $object)
    {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow('\InvalidArgumentException')
            ->duringCompleteOrder($event)
        ;
    }

    function it_completes_order(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);

        $order->complete()->shouldBeCalled();

        $this->completeOrder($event);
    }
}
