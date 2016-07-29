<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\OrderShippingTransitions;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class StateResolver implements StateResolverInterface
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
    public function resolvePaymentState(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);

        if (OrderPaymentStates::STATE_PAID === $order->getPaymentState()) {
            return;
        }

        if ($order->hasPayments()) {
            $completedPaymentTotal = 0;
            $payments = $order->getPayments()->filter(function (PaymentInterface $payment) {
                return PaymentInterface::STATE_COMPLETED === $payment->getState();
            });

            foreach ($payments as $payment) {
                $completedPaymentTotal += $payment->getAmount();
            }

            if (0 < $payments->count() && $completedPaymentTotal >= $order->getTotal()) {
                $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_PAY);

                return;
            }

            if ($completedPaymentTotal < $order->getTotal() && 0 < $completedPaymentTotal) {
                $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY);

                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resolveShippingState(OrderInterface $order)
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
    private function allShipmentsInStateButOrderStateNotUpdated(OrderInterface $order, $shipmentState, $orderShippingState)
    {
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

    /**
     * @param StateMachineInterface $stateMachine
     * @param string $transition
     */
    private function applyTransition(StateMachineInterface $stateMachine, $transition)
    {
        if ($stateMachine->can($transition)) {
            $stateMachine->apply($transition);
        }
    }
}
