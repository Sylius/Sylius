<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Entity;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\ShippingBundle\Entity\Shipment as BaseShipment;

/**
 * Shipment attached to order.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Shipment extends BaseShipment
{
    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Get the order.
     *
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the order.
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;
    }
}
