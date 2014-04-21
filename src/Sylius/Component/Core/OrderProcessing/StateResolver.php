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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * Order state resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StateResolver implements StateResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolvePaymentState(OrderInterface $order)
    {
        if ($order->hasPayments()) {
            /** @var $payments ArrayCollection */
            $payments = $order->getPayments();
            $completedPaymentTotal = 0;

            /** @var $payment PaymentInterface */
            foreach ($payments as $payment) {
                if ($payment->getState() === PaymentInterface::STATE_COMPLETED) {
                    $completedPaymentTotal += $payment->getAmount();
                }
            }

            // Payment is completed if we have received full amount
            if ($completedPaymentTotal === $order->getTotal()) {
                $order->setPaymentState(PaymentInterface::STATE_COMPLETED);
                return;
            }

            // Payment is processing / pending if one of the payment is.
            foreach(array(PaymentInterface::STATE_PROCESSING, PaymentInterface::STATE_PENDING) as $state) {
                /** @var $payment PaymentInterface */
                if ($payments->exists(function($key, $payment) use ($state) {
                    return $payment->getState() === $state;
                })) {
                    $order->setPaymentState(PaymentInterface::STATE_PROCESSING);
                    return;
                }
            }

            $order->setPaymentState(PaymentInterface::STATE_NEW);
        } else {
            $order->setPaymentState(PaymentInterface::STATE_NEW);
        }
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
