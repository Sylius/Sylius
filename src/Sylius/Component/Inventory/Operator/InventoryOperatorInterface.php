<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Operator;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\StockItemInterface;

/**
 * Stock operator interface.
 * Manage stock levels and inventory units.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface InventoryOperatorInterface
{
    /**
     * Increase stock on hand for given stock item by quantity.
     *
     * @param StockItemInterface $stockItem
     * @param integer            $quantity
     */
    public function increase(StockItemInterface $stockItem, $quantity);

    /**
     * Decrease stock by count of given inventory units.
     *
     * @param StockItemInterface $stockItem
     * @param integer            $quantity
     */
    public function decrease(StockItemInterface $stockItem, $quantity);

    /**
     * Hold stock for given stock item by quantity.
     *
     * @param StockItemInterface $stockItem
     * @param integer            $quantity
     */
    public function hold(StockItemInterface $stockItem, $quantity);

    /**
     * Release stock for given stock item by quantity.
     *
     * @param StockItemInterface $stockItem
     * @param integer            $quantity
     */
    public function release(StockItemInterface $stockItem, $quantity);
}
