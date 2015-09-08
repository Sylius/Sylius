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
use Sylius\Component\Inventory\Coordinator\CoordinatorInterface;
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
     * @var CoordinatorInterface
     */
    protected $coordinator;

    /**
     * Constructor.
     *
     * @param FactoryInterface $shipmentFactory
     * @param CoordinatorInterface $coordinator
     */
    public function __construct(FactoryInterface $shipmentFactory, CoordinatorInterface $coordinator)
    {
        $this->shipmentFactory = $shipmentFactory;
        $this->coordinator = $coordinator;
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
        }

        $packages = $this->coordinator->getPackages($order);

        foreach ($packages as $package) {

            $shipment->setStockLocation($package->getStockLocation());

            foreach ($package->getInventoryUnits() as $unit) {
                $shipment->addItem($unit);
            }

            $order->addShipment($shipment);
        }
    }
}
