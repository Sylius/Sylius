<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterCanceledOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelOrderPaymentCallback;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelOrderShipmentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelOrderShipmentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderShippingWorkflow): void
    {
        $this->beConstructedWith($syliusOrderShippingWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CancelOrderShipmentCallback::class);
    }

    function it_is_called_after_canceled_order(): void
    {
        $this->shouldImplement(AfterCanceledOrderCallbackInterface::class);
    }

    function it_cancels_payment(
        OrderInterface $order,
        WorkflowInterface $syliusOrderShippingWorkflow,
    ): void {
        $syliusOrderShippingWorkflow->can($order, 'cancel')->willReturn(true);

        $syliusOrderShippingWorkflow->apply($order, 'cancel')->shouldBeCalled();

        $this->call($order);
    }

    function it_does_nothing_when_transition_cannot_be_applied(
        OrderInterface $order,
        WorkflowInterface $syliusOrderShippingWorkflow,
    ): void {
        $syliusOrderShippingWorkflow->can($order, 'cancel')->willReturn(false);

        $syliusOrderShippingWorkflow->apply($order, 'cancel')->shouldNotBeCalled();

        $this->call($order);
    }
}
