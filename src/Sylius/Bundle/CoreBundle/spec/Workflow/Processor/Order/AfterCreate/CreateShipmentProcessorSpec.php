<?php

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate\CreateShipmentAfterOrderCreateProcessor;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

class CreateShipmentProcessorSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusShipmentWorkflow): void
    {
        $this->beConstructedWith($syliusShipmentWorkflow);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateShipmentAfterOrderCreateProcessor::class);
    }

    function it_creates_shipment(
        OrderInterface $order,
        WorkflowInterface $syliusShipmentWorkflow,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));

        $syliusShipmentWorkflow->apply($firstShipment, 'create')->shouldBeCalled();
        $syliusShipmentWorkflow->apply($secondShipment, 'create')->shouldBeCalled();

        $this->process($order);
    }
}
