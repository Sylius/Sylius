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
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * Shipment factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShipmentFactory implements OrderShipmentFactoryInterface
{
    /**
    * Shipment repository.
     *
     * @var FactoryInterface
     */
    protected $shipmentFactory;

    /**
     * Constructor.
     *
     * @param FactoryInterface $shipmentFactory
     */
    public function __construct(FactoryInterface $shipmentFactory)
    {
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createForOrder(OrderInterface $order)
    {
        if ($order->hasShipments()) {
            $shipment = $order->getShipments()->first();
        } else {
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
