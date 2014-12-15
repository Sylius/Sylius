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
interface StockableInterface extends InStockInterface
{


    /**
     * Is stockable available on demand?
     *
     * @return Boolean
     */
    public function isAvailableOnDemand();

    /**
     * Get stock on hold.
     *
     * @return integer
     */
    public function getOnHold();

    /**
     * Set stock on hold.
     *
     * @param integer
     */
    public function setOnHold($onHold);
}
