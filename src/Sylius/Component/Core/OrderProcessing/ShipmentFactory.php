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
use Sylius\Component\Resource\Manager\DomainManagerInterface;

/**
 * Shipment factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShipmentFactory implements ShipmentFactoryInterface
{
    /**
    * Shipment manager.
     *
     * @var DomainManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param DomainManagerInterface $manager
     */
    public function __construct(DomainManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function createShipment(OrderInterface $order)
    {
        if (!$order->getShipments()->isEmpty()) {
            $shipment = $order->getShipments()->first();
        } else {
            $shipment = $this->manager->createNew();
            $order->addShipment($shipment);
        }

        foreach ($order->getInventoryUnits() as $inventoryUnit) {
            if (null === $inventoryUnit->getShipment()) {
                $shipment->addItem($inventoryUnit);
            }
        }
    }
}
