<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterCompletedCheckoutCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\CreateOrderCallback;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\SaveCheckoutCompletionDateCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateOrderCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderWorkflow): void
    {
        $this->beConstructedWith($syliusOrderWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateOrderCallback::class);
    }

    function it_is_called_after_completed_checkout(): void
    {
        $this->shouldImplement(AfterCompletedCheckoutCallbackInterface::class);
    }

    function it_creates_order(
        OrderInterface $order,
        WorkflowInterface $syliusOrderWorkflow,
    ): void {
        $syliusOrderWorkflow->apply($order, 'create')->shouldBeCalled();

        $this->call($order);
    }
}
