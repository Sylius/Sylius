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

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface as BaseOrderItemUnitInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface OrderItemUnitInterface extends BaseOrderItemUnitInterface, ShipmentUnitInterface
{
    /**
     * @return StockableInterface
     */
    public function getStockable();

    /**
     * @return string
     */
    public function getInventoryName();

    /**
     * @return int
     */
    public function getTaxTotal();
}
