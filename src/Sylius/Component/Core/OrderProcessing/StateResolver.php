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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Order state resolver.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StateResolver implements StateResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolvePaymentState(OrderInterface $order)
    {
        $paymentState = PaymentInterface::STATE_NEW;

        if ($order->hasPayments()) {
            $payments = $order->getPayments();
            $completedPaymentTotal = 0;

            foreach ($payments as $payment) {
                if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
                    $completedPaymentTotal += $payment->getAmount();
                }
            }

            if ($completedPaymentTotal === $order->getTotal()) {
                // Payment is completed if we have received full amount.
                $paymentState = PaymentInterface::STATE_COMPLETED;
            } else {
                // Payment is processing if one of the payment is.
                if ($payments->exists(function ($key, $payment) {
                    return in_array($payment->getState(), array(
                        PaymentInterface::STATE_COMPLETED,
                        PaymentInterface::STATE_PROCESSING,
                        PaymentInterface::STATE_PENDING,
                    ));
                })) {
                    $paymentState = PaymentInterface::STATE_PROCESSING;
                }
            }
        }

        $order->setPaymentState($paymentState);
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

    protected function getShippingState(OrderInterface $order)
    {
        $states = array();

        foreach ($order->getShipments() as $shipment) {
            $states[] = $shipment->getState();
        }

        $states = array_unique($states);

        $acceptableStates = array(
            ShipmentInterface::STATE_CHECKOUT   => OrderShippingStates::CHECKOUT,
            ShipmentInterface::STATE_ONHOLD     => OrderShippingStates::ONHOLD,
            ShipmentInterface::STATE_READY      => OrderShippingStates::READY,
            ShipmentInterface::STATE_SHIPPED    => OrderShippingStates::SHIPPED,
            ShipmentInterface::STATE_RETURNED   => OrderShippingStates::RETURNED,
            ShipmentInterface::STATE_CANCELLED  => OrderShippingStates::CANCELLED,
        );

        foreach ($acceptableStates as $shipmentState => $orderState) {
            if (array($shipmentState) == $states) {
                return $orderState;
            }
        }

        return OrderShippingStates::PARTIALLY_SHIPPED;
    }
}
