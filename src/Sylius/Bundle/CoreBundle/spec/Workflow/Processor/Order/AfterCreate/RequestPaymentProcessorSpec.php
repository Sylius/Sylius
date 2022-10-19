<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate\RequestPaymentProcessor;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentProcessorSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusOrderPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestPaymentProcessor::class);
    }

    function it_requests_payment(
        OrderInterface $order,
        WorkflowInterface $syliusOrderPaymentWorkflow,
    ): void {
        $syliusOrderPaymentWorkflow->apply($order, 'request_payment')->shouldBeCalled();

        $this->process($order);
    }
}
