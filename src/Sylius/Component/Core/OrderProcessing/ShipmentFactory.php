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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Shipment factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
     * @var CoordinatorInterface
     */
    protected $coordinator;

    /**
     * Constructor.
     *
     * @param RepositoryInterface  $shipmentRepository
     * @param CoordinatorInterface $coordinator
     */
    public function __construct(RepositoryInterface $shipmentRepository, CoordinatorInterface $coordinator)
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->coordinator = $coordinator;
    }

    /**
     * {@inheritdoc}
     */
    public function createShipments(OrderInterface $order)
    {
        if ($order->hasShipments()) {
            return;
        }

        $packages = $this->coordinator->getPackages($order);

        foreach ($packages as $package) {
            $shipment = $this->shipmentRepository->createNew();

            $shipment->setStockLocation($package->getStockLocation());

            foreach ($package->getInventoryUnits() as $unit) {
                $shipment->addItem($unit);
            }

            $order->addShipment($shipment);
        }
    }
}
