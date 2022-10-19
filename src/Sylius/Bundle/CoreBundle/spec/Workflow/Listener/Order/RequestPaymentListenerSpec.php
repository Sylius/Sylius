<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\RequestPaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentListenerSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusOrderPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestPaymentListener::class);
    }

    function it_requests_payment(
        Event $event,
        OrderInterface $order,
        WorkflowInterface $syliusOrderPaymentWorkflow,
    ): void {
        $event->getSubject()->willReturn($order);

        $syliusOrderPaymentWorkflow->apply($order, 'request_payment')->shouldBeCalled();

        $this->requestPayment($event);
    }
}
