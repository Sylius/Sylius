<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CheckoutStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @param FactoryInterface $stateMachineFactory
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     */
    public function __construct(
        FactoryInterface $stateMachineFactory,
        ShippingMethodsResolverInterface $shippingMethodsResolver
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        if (
            (!$this->isShippingRequired($order) || $this->isOnlyOneShippingMethodAvailableForEachShipment($order))
            && $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
        ) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING);
        }

        if (0 === $order->getTotal() && $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT);
        }
    }


    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isShippingRequired(OrderInterface $order)
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->getVariant()->isShippingRequired()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isOnlyOneShippingMethodAvailableForEachShipment(OrderInterface $order)
    {
        if (!$order->hasShipments()) {
            return false;
        }

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            if (1 !== count($this->shippingMethodsResolver->getSupportedMethods($shipment))) {
                return false;
            }
        }

        return true;
    }
}
