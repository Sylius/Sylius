<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

use Doctrine\Common\Collections\Collection;


/**
 * StockItemInterface model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemInterface
{
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
     *
     * $return StockItemInterface
     */
    public function setStockable(StockableInterface $stockable);

    /**
     * Set the stock location
     *
     * @param StockLocationInterface $location
     *
     * @return StockItemInterface
     */
    public function setStockLocation(StockLocationInterface $location);

    /**
     * Get the stock location
     *
     * @return StockLocationInterface
     */
    public function getStockLocation();

    /**
     * @return StockMovement[]|Collection
     */
    public function getMovements();

    /**
     * Add a stock movement
     *
     * @param $movement
     *
     * @return StockItemInterface
     */
    public function addMovement(StockMovementInterface $movement);

    /**
     * Remove a stock movement
     *
     * @param $movement
     *
     * @return StockItemInterface
     */
    public function removeMovement(StockMovementInterface $movement);
}