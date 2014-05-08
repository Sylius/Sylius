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

use Sylius\Component\Inventory\Model\InventoryUnitInterface as BaseInventoryUnitInterface;
use Sylius\Component\Shipping\Model\ShipmentItemInterface;

/**
 * Inventory unit interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface InventoryUnitInterface extends BaseInventoryUnitInterface, ShipmentItemInterface
{
    /**
     * @return null|OrderItemInterface
     */
    public function getOrderItem();

    /**
     * @param null|OrderItemInterface $orderItem
     */
    public function setOrderItem(OrderItemInterface $orderItem = null);
}
