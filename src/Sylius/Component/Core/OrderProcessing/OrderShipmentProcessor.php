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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderShipmentProcessor implements OrderProcessorInterface
{
    /**
     * @var DefaultShippingMethodResolverInterface
     */
    private $defaultShippingMethodResolver;

    /**
     * @var FactoryInterface
     */
    private $shipmentFactory;

    /**
     * @param DefaultShippingMethodResolverInterface $defaultShippingMethodResolver
     * @param FactoryInterface $shipmentFactory
     */
    public function __construct(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory
    ) {
        $this->defaultShippingMethodResolver = $defaultShippingMethodResolver;
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        $shipment = $this->getOrderShipment($order);

        if (null === $shipment) {
            return;
        }

        foreach ($order->getItemUnits() as $itemUnit) {
            if (null === $itemUnit->getShipment()) {
                $shipment->addUnit($itemUnit);
            }
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return ShipmentInterface
     */
    private function getOrderShipment(OrderInterface $order)
    {
        if ($order->isEmpty()) {
            $order->removeShipments();

            return null;
        }

        if ($order->hasShipments()) {
            return $order->getShipments()->first();
        }

        try {
            /** @var ShipmentInterface $shipment */
            $shipment = $this->shipmentFactory->createNew();
            $shipment->setOrder($order);
            $shipment->setMethod($this->defaultShippingMethodResolver->getDefaultShippingMethod($shipment));

            $order->addShipment($shipment);

            return $shipment;
        } catch (UnresolvedDefaultShippingMethodException $exception) {
            return null;
        }
    }
}
