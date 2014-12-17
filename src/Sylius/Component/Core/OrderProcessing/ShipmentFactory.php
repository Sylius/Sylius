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
use Sylius\Component\Inventory\Operator\Coordinator;
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

    protected $coordinator;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $shipmentRepository
     */
    public function __construct(RepositoryInterface $shipmentRepository, Coordinator $coordinator)
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->coordinator = $coordinator;
    }

    /**
     * {@inheritdoc}
     */
    public function createShipment(OrderInterface $order)
    {

        $shipments = $this->coordinator->getShipments($order);

        foreach($shipments as $shipment) {
            $order->addShipment($shipment);
        }
    }
}
