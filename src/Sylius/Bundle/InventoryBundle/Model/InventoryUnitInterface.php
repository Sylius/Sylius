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
    const STATE_UNAVAILABLE = 0;
    const STATE_AVAILABLE   = 1;

    /**
     * Get inventory unit id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Set inventory unit id.
     *
     * @param mixed $id
     */
    function setId($id);

    /**
     * Get related stockable object id.
     *
     * @return mixed
     */
    function getStockableId();

    /**
     * Set stockable object id.
     *
     * @param mixed $stockableId
     */
    function setStockableId($stockableId);

    /**
     * Get inventory unit state.
     *
     * @return integer
     */
    function getState();

    /**
     * Set inventory unit state.
     *
     * @param integer $state
     */
    function setState($state);
}
