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
     * Simply checks if there any stock available.
     * It should also return true for items available on demand.
     *
     * @return Boolean
     */
    public function isInStock();

    /**
     * Is stockable available on demand?
     *
     * @return Boolean
     */
    public function isAvailableOnDemand();

    /**
     * Get stock on hand.
     *
     * @return integer
     */
    public function getOnHand();

    /**
     * Set stock on hand.
     *
     * @param integer $onHand
     */
    public function setOnHand($onHand);
}
