<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Webmozart\Assert\Assert;

final class OrderShippingStateResolver implements StateResolverInterface
{
    public function __construct(private FactoryInterface $stateMachineFactory)
    {
    }

    public function resolve(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (OrderShippingStates::STATE_SHIPPED === $order->getShippingState()) {
            return;
        }
        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($order, OrderShippingTransitions::GRAPH);

        if ($this->allShipmentsInStateButOrderStateNotUpdated($order, ShipmentInterface::STATE_SHIPPED, OrderShippingStates::STATE_SHIPPED)) {
            $stateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP);
        }

        if ($this->isPartiallyShippedButOrderStateNotUpdated($order)) {
            $stateMachine->apply(OrderShippingTransitions::TRANSITION_PARTIALLY_SHIP);
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
