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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderPaymentStates;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StateResolver implements StateResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolvePaymentState(OrderInterface $order)
    {
        if ($order->hasPayments()) {
            $payments = $order->getPayments();
            $completedPaymentTotal = 0;

            foreach ($payments as $payment) {
                if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
                    $completedPaymentTotal += $payment->getAmount();
                }
            }

            if (OrderInterface::STATE_CANCELLED === $order->getState()) {
                $order->setPaymentState(OrderPaymentStates::STATE_CANCELLED);

                return;
            }

            if (OrderInterface::STATE_FULFILLED === $order->getState() && $completedPaymentTotal >= $order->getTotal()) {
                $order->setPaymentState(OrderPaymentStates::STATE_PAID);

                return;
            }

            if ($completedPaymentTotal < $order->getTotal() && 0 < $completedPaymentTotal) {
                $order->setPaymentState(OrderPaymentStates::STATE_PARTIALLY_PAID);

                return;
            }
        }

        $order->setPaymentState(OrderPaymentStates::STATE_AWAITING_PAYMENT);
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
}
