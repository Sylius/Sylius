<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

/**
 * Stockable interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockableInterface
{
    /**
     * Get stock keeping unit.
     *
     * @return mixed
     */
    public function getSku();

    /**
     * Get inventory displayed name.
     *
     * @return string
     */
    public function getInventoryName();

    /**
     * Get stock on hand.
     *
     * @return integer
     */
    public function getStock();

    /**
     * Set Stock.
     *
     * @param entity $stock
     */
    public function setStock($stock);
}
