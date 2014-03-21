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

use Sylius\Component\Shipping\Model\Shipment as BaseShipment;

/**
 * Shipment attached to order.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Shipment extends BaseShipment implements ShipmentInterface
{
    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

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
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;

        return $this;
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
