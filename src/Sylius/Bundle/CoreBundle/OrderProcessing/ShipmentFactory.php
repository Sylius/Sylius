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
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Shipment factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShipmentFactory implements ShipmentFactoryInterface
{
    /**
    * Shipment repository.
     *
     * @var RepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $shipmentRepository
     */
    public function __construct(RepositoryInterface $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createShipment(OrderInterface $order, ShippingMethodInterface $method = null)
    {
        $shipment = $this->shipmentRepository->createNew();
        $shipment->setMethod($method);

        foreach ($order->getInventoryUnits() as $inventoryUnit) {
            $shipment->addItem($inventoryUnit);
        }

        $order->addShipment($shipment);
    }
}
