<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout\CreateOrderListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateOrderListenerSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderWorkflow): void
    {
        $this->beConstructedWith($syliusOrderWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateOrderListener::class);
    }

    function it_creates_orders(
        Event $event,
        OrderInterface $order,
        WorkflowInterface $syliusOrderWorkflow,
    ): void {
        $event->getSubject()->willReturn($order);

        $syliusOrderWorkflow->apply($order, 'create')->shouldBeCalled();

        $this->createOrder($event);
    }
}
