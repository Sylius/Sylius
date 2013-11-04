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

use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface as BaseInventoryUnitInterface;
use Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface;

/**
 * Inventory unit interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface InventoryUnitInterface extends BaseInventoryUnitInterface, ShipmentItemInterface
{
    /**
     * @return null|OrderInterface
     */
    public function getOrder();

    /**
     * @param null|OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);
}
