<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderShippingStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
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

    /**
     * @param OrderInterface $order
     * @param string $shipmentState
     *
     * @return int
     */
    private function countOrderShipmentsInState(OrderInterface $order, $shipmentState)
    {
        $shipments = $order->getShipments();

        return $shipments
            ->filter(function (ShipmentInterface $shipment) use ($shipmentState) {
                return $shipment->getState() === $shipmentState;
            })
            ->count()
        ;
    }

    /**
     * @param OrderInterface $order
     * @param string $shipmentState
     * @param string $orderShippingState
     *
     * @return bool
     */
    private function allShipmentsInStateButOrderStateNotUpdated(
        OrderInterface $order,
        $shipmentState,
        $orderShippingState
    ) {
        $shipmentInStateAmount = $this->countOrderShipmentsInState($order, $shipmentState);
        $shipmentAmount = $order->getShipments()->count();

        return $shipmentAmount === $shipmentInStateAmount && $orderShippingState !== $order->getShippingState();
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isPartiallyShippedButOrderStateNotUpdated(OrderInterface $order)
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
