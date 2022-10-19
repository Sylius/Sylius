<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateShipmentListener
{
    public function __construct(private WorkflowInterface $syliusShipmentWorkflow)
    {
    }

    public function createShipment(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        foreach ($order->getShipments() as $shipment) {
            $this->syliusShipmentWorkflow->apply($shipment, ShipmentTransitions::TRANSITION_CREATE);
        }
    }
}
