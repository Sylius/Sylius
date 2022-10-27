<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterCanceledOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelOrderPaymentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelOrderPaymentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusOrderPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CancelOrderPaymentCallback::class);
    }

    function it_is_called_after_canceled_order(): void
    {
        $this->shouldImplement(AfterCanceledOrderCallbackInterface::class);
    }

    function it_cancels_payment(
        OrderInterface $order,
        WorkflowInterface $syliusOrderPaymentWorkflow,
    ): void {
        $syliusOrderPaymentWorkflow->can($order, 'cancel')->willReturn(true);

        $syliusOrderPaymentWorkflow->apply($order, 'cancel')->shouldBeCalled();

        $this->call($order);
    }

    function it_does_nothing_when_transition_cannot_be_applied(
        OrderInterface $order,
        WorkflowInterface $syliusOrderPaymentWorkflow,
    ): void {
        $syliusOrderPaymentWorkflow->can($order, 'cancel')->willReturn(false);

        $syliusOrderPaymentWorkflow->apply($order, 'cancel')->shouldNotBeCalled();

        $this->call($order);
    }
}
