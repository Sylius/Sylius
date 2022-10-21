<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelPaymentCallback;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelShipmentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelShipmentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusShipmentWorkflow): void
    {
        $this->beConstructedWith($syliusShipmentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CancelShipmentCallback::class);
    }

    function it_cancels_payment(
        OrderInterface    $order,
        WorkflowInterface $syliusShipmentWorkflow,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));

        $syliusShipmentWorkflow->can($firstShipment, 'cancel')->willReturn(false);
        $syliusShipmentWorkflow->can($secondShipment, 'cancel')->willReturn(true);

        $syliusShipmentWorkflow->apply($firstShipment, 'cancel')->shouldNotBeCalled();
        $syliusShipmentWorkflow->apply($secondShipment, 'cancel')->shouldBeCalled();

        $this->call($order);
    }
}
