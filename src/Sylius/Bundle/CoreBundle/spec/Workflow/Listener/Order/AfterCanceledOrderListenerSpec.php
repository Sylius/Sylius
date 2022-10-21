<?php

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterCanceledOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\AfterCanceledOrderListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterCanceledOrderListenerSpec extends ObjectBehavior
{
    function let(
        AfterCanceledOrderCallbackInterface $firstCallback,
        AfterCanceledOrderCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterCanceledOrderListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        OrderInterface $order,
        AfterCanceledOrderCallbackInterface $firstCallback,
        AfterCanceledOrderCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($order);

        $firstCallback->call($order)->shouldBeCalled();
        $secondCallback->call($order)->shouldBeCalled();

        $this->call($event);
    }
}
