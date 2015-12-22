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
use Sylius\Component\Order\Model\OrderItemUnitInterface as BaseOrderItemUnitInterface;
use Sylius\Component\Shipping\Model\ShipmentItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface OrderItemUnitInterface extends BaseOrderItemUnitInterface, BaseInventoryUnitInterface, ShipmentItemInterface
{
    /**
     * @return OrderItemInterface
     */
    public function getOrderItem();

    /**
     * @param null|OrderItemInterface $orderItem
     */
    public function setOrderItem(OrderItemInterface $orderItem = null);
}
