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
use SM\Factory\Factory;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Core\OrderCheckoutStates;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StateResolver implements StateResolverInterface
{
    private $smFactory;

    public function __construct(Factory $smFactory)
    {
        $this->smFactory = $smFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolvePaymentState(OrderInterface $order)
    {
        // do not recalculate payment state. althoguh the payment may have been
        // refunded, the payment WAS completed for this order.
        if ($order->getPaymentState() === OrderInterface::STATE_PAYMENT_COMPLETED) {
            return;
        }

        // do not calculate payment state when checkout has not been completed
        if ($order->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED) {
            return;
        }

        // order has no payments yet, leave in default state.
        if (!$order->hasPayments()) {
            return;
        }

        $stateMachine = $this->smFactory->get($order, 'sylius_order_payment');

        if ($this->isPaymentCompleted($order)) {
            $stateMachine->apply(OrderTransitions::SYLIUS_PAYMENT_COMPLETE);
            return;
        }

        if ($this->hasPendingPayments($order)) {
            $stateMachine->apply(OrderTransitions::SYLIUS_PAYMENT_WAIT);
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
            ShipmentInterface::STATE_CHECKOUT => OrderShippingStates::CHECKOUT,
            ShipmentInterface::STATE_ONHOLD => OrderShippingStates::ONHOLD,
            ShipmentInterface::STATE_READY => OrderShippingStates::READY,
            ShipmentInterface::STATE_SHIPPED => OrderShippingStates::SHIPPED,
            ShipmentInterface::STATE_RETURNED => OrderShippingStates::RETURNED,
            ShipmentInterface::STATE_CANCELLED => OrderShippingStates::CANCELLED,
        ];

        foreach ($acceptableStates as $shipmentState => $orderState) {
            if ([$shipmentState] == $states) {
                return $orderState;
            }
        }

        return OrderShippingStates::PARTIALLY_SHIPPED;
    }

    private function isPaymentCompleted(OrderInterface $order)
    {
        $payments = $order->getPayments();
        $completedTotal = 0;

        foreach ($payments as $payment) {
            if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
                continue;
            }

            $completedTotal += $payment->getAmount();
        }

        if ($completedTotal >= $order->getTotal()) {
            return true;
        }

        return false;
    }

    private function hasPendingPayments(OrderInterface $order)
    {
        $payments = $order->getPayments();

        return $payments->exists(function ($key, $payment) {
            return in_array($payment->getState(), [
                PaymentInterface::STATE_PROCESSING,
                PaymentInterface::STATE_PENDING,
            ]);
        });
    }
}
