<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\AssignNumberListener;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AssignNumberListenerSpec extends ObjectBehavior
{
    function let(OrderNumberAssignerInterface $orderNumberAssigner): void
    {
        $this->beConstructedWith($orderNumberAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignNumberListener::class);
    }

    function it_assigns_order_numbers(
        Event $event,
        OrderInterface $order,
        OrderNumberAssignerInterface $orderNumberAssigner,
    ): void {
        $event->getSubject()->willReturn($order);

        $orderNumberAssigner->assignNumber($order)->shouldBeCalled();

        $this->assignNumber($event);
    }
}
