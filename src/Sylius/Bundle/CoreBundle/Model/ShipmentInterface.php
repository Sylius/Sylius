<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface as BaseShipmentInterface;

/**
 * Shipment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ShipmentInterface extends BaseShipmentInterface
{
    /**
     * Get the order.
     *
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Set the order.
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);
}
