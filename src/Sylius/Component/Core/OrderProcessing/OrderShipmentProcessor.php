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
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShipmentProcessor implements OrderShipmentProcessorInterface
{
    /**
     * @var DefaultShippingMethodResolverInterface
     */
    protected $defaultShippingMethodResolver;

    /**
     * @var FactoryInterface
     */
    protected $shipmentFactory;

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
    public function processOrderShipment(OrderInterface $order)
    {
        $shipment = $this->getOrderShipment($order);

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
        if ($order->hasShipments()) {
            return $order->getShipments()->first();
        }

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentFactory->createNew();
        $order->addShipment($shipment);
        $shipment->setMethod($this->defaultShippingMethodResolver->getDefaultShippingMethod($shipment));

        return $shipment;
    }
}
