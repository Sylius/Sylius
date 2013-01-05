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
 * Inventory unit interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface InventoryUnitInterface
{
    /**
     * Default states.
     */
    const STATE_SOLD        = 'sold';
    const STATE_BACKORDERED = 'backordered';

    /**
     * Get inventory unit id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Get related stockable object.
     *
     * @return StockableInterface
     */
    function getStockable();

    /**
     * Set stockable object.
     *
     * @param $stockable StockableInterface
     */
    function setStockable(StockableInterface $stockable);

    /**
     * Get the SKU of stockable.
     *
     * @return string
     */
    function getSku();

    /**
     * Get displayed inventory name
     *
     * @return string
     */
    function getInventoryName();

    /**
     * Get inventory unit state.
     *
     * @return integer
     */
    function getInventoryState();

    /**
     * Set inventory unit state.
     *
     * @param integer $state
     */
    function setInventoryState($state);

    /**
     * Is in "sold" state?
     *
     * @return Boolean
     */
    function isSold();

    /**
     * Is a backordered inventory unit?
     *
     * @return Boolean
     */
    function isBackordered();

    /**
     * Get creation time.
     *
     * @return \DateTime
     */
    function getCreatedAt();

    /**
     * Get last update time.
     *
     * @return \DateTime
     */
    function getUpdatedAt();
}
