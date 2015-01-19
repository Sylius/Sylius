<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Manager;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Inventory Manager interface.
 *
 * @author Myke Hines <myke@webhines.com>
 */
interface InventoryManagerInterface
{
    /**
     * Checks whether stockable object is available in stock.
     * Takes required quantity into account.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     *
     * @return Boolean
     */
    public function isStockAvailable(StockableInterface $stockable, $quantity);

    /**
     * Checks to see if stock in convertable (into a quote or order)
     * Includes checks for min/max quantity, etc..
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     *
     * @return Boolean
     */
    public function isStockConvertable(StockableInterface $stockable, $quantity);

}
