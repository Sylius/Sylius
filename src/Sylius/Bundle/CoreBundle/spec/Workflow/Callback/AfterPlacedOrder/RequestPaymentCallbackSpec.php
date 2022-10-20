<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder\RequestPaymentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusOrderPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestPaymentCallback::class);
    }

    function it_requests_payment(
        OrderInterface $order,
        WorkflowInterface $syliusOrderPaymentWorkflow,
    ): void {
        $syliusOrderPaymentWorkflow->apply($order, 'request_payment')->shouldBeCalled();

        $this->run($order);
    }
}
