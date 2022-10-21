<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\StateResolver;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderShippingStateResolver implements OrderShippingStateResolverInterface
{
    public function __construct(private WorkflowInterface $syliusOrderShippingWorkflow)
    {
    }

    public function resolve(OrderInterface $order): void
    {
        if (OrderShippingStates::STATE_SHIPPED === $order->getShippingState()) {
            return;
        }

        if ($this->allShipmentsInStateButOrderStateNotUpdated($order, ShipmentInterface::STATE_SHIPPED, OrderShippingStates::STATE_SHIPPED)) {
            $this->syliusOrderShippingWorkflow->apply($order, OrderShippingTransitions::TRANSITION_SHIP);
        }

        if ($this->isPartiallyShippedButOrderStateNotUpdated($order)) {
            $this->syliusOrderShippingWorkflow->apply($order, OrderShippingTransitions::TRANSITION_PARTIALLY_SHIP);
        }
    }

    private function countOrderShipmentsInState(OrderInterface $order, string $shipmentState): int
    {
        $shipments = $order->getShipments();

        return $shipments
            ->filter(function (ShipmentInterface $shipment) use ($shipmentState) {
                return $shipment->getState() === $shipmentState;
            })
            ->count()
            ;
    }

    private function allShipmentsInStateButOrderStateNotUpdated(
        OrderInterface $order,
        string $shipmentState,
        string $orderShippingState,
    ): bool {
        $shipmentInStateAmount = $this->countOrderShipmentsInState($order, $shipmentState);
        $shipmentAmount = $order->getShipments()->count();

        return $shipmentAmount === $shipmentInStateAmount && $orderShippingState !== $order->getShippingState();
    }

    private function isPartiallyShippedButOrderStateNotUpdated(OrderInterface $order): bool
    {
        $shipmentInShippedStateAmount = $this->countOrderShipmentsInState($order, ShipmentInterface::STATE_SHIPPED);
        $shipmentAmount = $order->getShipments()->count();

        return
            1 <= $shipmentInShippedStateAmount &&
            $shipmentInShippedStateAmount < $shipmentAmount &&
            OrderShippingStates::STATE_PARTIALLY_SHIPPED !== $order->getShippingState()
        ;
    }
}
