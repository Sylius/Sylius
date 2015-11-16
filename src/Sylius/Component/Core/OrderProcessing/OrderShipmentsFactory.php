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
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;

/**
 * Shipment factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShipmentsFactory implements OrderShipmentsFactoryInterface
{
    /**
     * @var ResourceFactoryInterface
     */
    private $shipmentFactory;

    /**
     * OrderShipmentsFactory constructor.
     *
     * @param ResourceFactoryInterface $shipmentFactory
     */
    public function __construct(ResourceFactoryInterface $shipmentFactory)
    {
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createForOrder(OrderInterface $order)
    {
        $shipment = $order->getShipments()->first();

        if (!$shipment) {
            $shipment = $this->shipmentFactory->createNew();
            $order->addShipment($shipment);
        }

        foreach ($order->getInventoryUnits() as $inventoryUnit) {
            if (null === $inventoryUnit->getShipment()) {
                $shipment->addItem($inventoryUnit);
            }
        }
    }
}
