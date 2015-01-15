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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Inventory unit interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface InventoryUnitInterface extends TimestampableInterface
{
    /**
     * Default states.
     */
    const STATE_CHECKOUT    = 'checkout';
    const STATE_ONHOLD      = 'onhold';
    const STATE_SOLD        = 'sold';
    const STATE_BACKORDERED = 'backordered';
    const STATE_RETURNED    = 'returned';

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
     * @return string
     */
    public function getInventoryState();

    /**
     * Set inventory unit state.
     *
     * @param string $state
     */
    public function setInventoryState($state);

    /**
     * Is in "sold" state?
     *
     * @return bool
     */
    public function isSold();

    /**
     * Is a backordered inventory unit?
     *
     * @return bool
     */
    public function isBackordered();
}
