<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Inventory\Model\StockLocationInterface as BaseStockLocationInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;

/**
 * Shipment attached to order.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Shipment extends BaseShipment implements ShipmentInterface
{
    /**
     * Order.
     *
     * @var BaseOrderInterface
     */
    protected $order;

     /**
     * StockLocation.
     *
     * @var BaseStockLocationInterface
     */
    protected $stockLocation;

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(BaseOrderInterface $order = null)
    {
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockLocation()
    {
        return $this->stockLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function setStockLocation(BaseStockLocationInterface $location = null)
    {
        $this->stockLocation = $location;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingWeight()
    {
        $weight = 0;

        foreach ($this->items as $item) {
            $weight += $item->getShippable()->getShippingWeight();
        }

        return $weight;
    }
}
