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
use Sylius\Component\Inventory\Model\StockableInterface;

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
     * Increase stock on hand for given stockable by quantity.
     *
     * @param StockableInterface $stockable
     * @param int            $quantity
     */
    public function increase(StockableInterface $stockable, $quantity);

    /**
     * Hold stock for given stockable by quantity.
     *
     * @param StockableInterface $stockable
     * @param int            $quantity
     */
    public function hold(StockableInterface $stockable, $quantity);

    /**
     * Release stock for given stockable by quantity.
     *
     * @param StockableInterface $stockable
     * @param int            $quantity
     */
    public function release(StockableInterface $stockable, $quantity);

    /**
     * Decrease stock by count of given inventory units.
     *
     * @param StockableInterface[]|Collection $inventoryUnits
     */
    public function decrease($inventoryUnits);
}
