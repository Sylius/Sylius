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

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Inventory unit interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface InventoryUnitInterface extends TimestampableInterface
{
    /**
     * Default states.
     */
    const STATE_CHECKOUT    = 'checkout';
    const STATE_SOLD        = 'sold';
    const STATE_BACKORDERED = 'backordered';

    /**
     * Get related stockable object.
     *
     * @return StockableInterface
     */
    public function getStockable();

    /**
     * Set stockable object.
     *
     * @param $stockable StockableInterface
     */
    public function setStockable(StockableInterface $stockable);

    /**
     * Get the SKU of stockable.
     *
     * @return string
     */
    public function getSku();

    /**
     * Get displayed inventory name
     *
     * @return string
     */
    public function getInventoryName();

    /**
     * Get inventory unit state.
     *
     * @return integer
     */
    public function getInventoryState();

    /**
     * Set inventory unit state.
     *
     * @param integer $state
     */
    public function setInventoryState($state);

    /**
     * Is in "sold" state?
     *
     * @return Boolean
     */
    public function isSold();

    /**
     * Is a backordered inventory unit?
     *
     * @return Boolean
     */
    public function isBackordered();
}
