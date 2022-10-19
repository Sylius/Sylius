<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\RequestShippingListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestShippingListenerSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderShippingWorkflow): void
    {
        $this->beConstructedWith($syliusOrderShippingWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestShippingListener::class);
    }

    function it_requests_shipping(
        Event $event,
        OrderInterface $order,
        WorkflowInterface $syliusOrderShippingWorkflow,
    ): void {
        $event->getSubject()->willReturn($order);

        $syliusOrderShippingWorkflow->apply($order, 'request_shipping')->shouldBeCalled();

        $this->requestShipping($event);
    }
}
