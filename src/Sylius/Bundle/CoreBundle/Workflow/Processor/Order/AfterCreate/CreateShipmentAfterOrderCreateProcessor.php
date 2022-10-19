<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateShipmentAfterOrderCreateProcessor implements AfterOrderCreateProcessorInterface
{
    public function __construct(private WorkflowInterface $syliusShipmentWorkflow)
    {
    }

    public function process(OrderInterface $order): void
    {
        foreach ($order->getShipments() as $shipment) {
            $this->syliusShipmentWorkflow->apply($shipment, ShipmentTransitions::TRANSITION_CREATE);
        }
    }
}
