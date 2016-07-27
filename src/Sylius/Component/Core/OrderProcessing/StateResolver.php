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
use Sylius\Component\Core\OrderPaymentTransitions;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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

        if ($order->hasPayments()) {
            $payments = $order->getPayments();
            $completedPaymentTotal = 0;

            foreach ($payments as $payment) {
                if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
                    $completedPaymentTotal += $payment->getAmount();
                }
            }

            if (OrderInterface::STATE_CANCELLED === $order->getState()) {
                $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_CANCEL);

                return;
            }

            if (OrderInterface::STATE_FULFILLED === $order->getState() && $completedPaymentTotal >= $order->getTotal()) {
                $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_PAY);

                return;
            }

            if ($completedPaymentTotal < $order->getTotal() && 0 < $completedPaymentTotal) {
                $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY);

                return;
            }
        }

        $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveShippingState(OrderInterface $order)
    {
        if ($order->isBackorder()) {
            $order->setShippingState(OrderShippingStates::BACKORDER);

            return;
        }

        $order->setShippingState($this->getShippingState($order));
    }

    /**
     * @param OrderInterface $order
     *
     * @return string
     */
    protected function getShippingState(OrderInterface $order)
    {
        $states = [];

        foreach ($order->getShipments() as $shipment) {
            $states[] = $shipment->getState();
        }

        $states = array_unique($states);

        $acceptableStates = [
            ShipmentInterface::STATE_READY => OrderShippingStates::READY,
            ShipmentInterface::STATE_SHIPPED => OrderShippingStates::SHIPPED,
            ShipmentInterface::STATE_CANCELLED => OrderShippingStates::CANCELLED,
        ];

        foreach ($acceptableStates as $shipmentState => $orderState) {
            if ([$shipmentState] == $states) {
                return $orderState;
            }
        }

        return OrderShippingStates::PARTIALLY_SHIPPED;
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
