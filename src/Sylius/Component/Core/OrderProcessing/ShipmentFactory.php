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
use Sylius\Component\Inventory\Packaging\PackerInterface;
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
     * @var PackerInterface
     */
    protected $packer;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $shipmentRepository
     * @param PackerInterface     $packer
     */
    public function __construct(RepositoryInterface $shipmentRepository, PackerInterface $packer)
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->packer = $packer;
    }

    /**
     * {@inheritdoc}
     */
    public function createShipments(OrderInterface $order)
    {
        if (!$order->getShipments()->isEmpty()) {
            return;
        }

        $packages = $this->packer->pack($order->getInventoryUnits());

        foreach ($packages as $package) {
            $shipment = $this->shipmentRepository->createNew();

            $shipment->setStockLocation($package->getLocation());

            foreach ($package->getInventoryUnits() as $unit) {
                $shipment->addInventoryUnit($unit);
            }

            $order->addShipment($shipment);
        }
    }
}
