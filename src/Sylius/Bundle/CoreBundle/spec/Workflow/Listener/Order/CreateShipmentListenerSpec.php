<?php

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\CreateShipmentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

class CreateShipmentListenerSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusShipmentWorkflow): void
    {
        $this->beConstructedWith($syliusShipmentWorkflow);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateShipmentListener::class);
    }

    function it_creates_shipment(
        Event $event,
        OrderInterface $order,
        WorkflowInterface $syliusShipmentWorkflow,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
    ): void {
        $event->getSubject()->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));

        $syliusShipmentWorkflow->apply($firstShipment, 'create')->shouldBeCalled();
        $syliusShipmentWorkflow->apply($secondShipment, 'create')->shouldBeCalled();

        $this->createShipment($event);
    }
}
