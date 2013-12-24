<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderShippingStates;
use Sylius\Bundle\CoreBundle\Model\ShipmentInterface;

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

    private function getShippingState(OrderInterface $order)
    {
        $states = array();
        foreach ($order->getShipments() as $shipment) {
            $states[] = $shipment->getState();
        }

        $states = array_unique($states);

        if (array(ShipmentInterface::STATE_SHIPPED) === $states) {
            return OrderShippingStates::SHIPPED;
        }

        if (array(ShipmentInterface::STATE_DISPATCHED) === $states) {
            return OrderShippingStates::DISPATCHED;
        }

        if (array(ShipmentInterface::STATE_RETURNED) === $states) {
            return OrderShippingStates::RETURNED;
        }

        if (array(ShipmentInterface::STATE_READY) === $states) {
            return OrderShippingStates::READY;
        }

        return OrderShippingStates::PARTIALLY_SHIPPED;
    }
}
