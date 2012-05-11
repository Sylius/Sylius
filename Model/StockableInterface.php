<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Model;

/**
 * Stockable interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface StockableInterface
{
    /**
     * Get stockable object id.
     *
     * @return mixed
     */
    function getStockableId();

    /**
     * Shortcut method for implementations that don't need full inventory tracking.
     *
     * @return Boolean
     */
    function inStock();

    /**
     * Get stock on hand.
     *
     * @return integer
     */
    function getOnHand();

    /**
     * Set stock on hand.
     *
     * @param integer $onHand
     */
    function setOnHand($onHand);
}
