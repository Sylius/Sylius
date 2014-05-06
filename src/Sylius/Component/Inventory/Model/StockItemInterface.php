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
 * Stock item interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemInterface extends TimestampableInterface
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
     */
    public function setStockable(StockableInterface $stockable);

    public function getLocation();
    public function setLocation(StockLocationInterface $location);

    public function getStockMovements();
    public function addStockMovement(StockMovementInterface $movement);
    public function removeStockMovement(StockMovementInterface $movement);
    public function hasStockMovement(StockMovementInterface $movement);

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

    /**
     * Is stockable available on demand?
     *
     * @return Boolean
     */
    public function isAvailableOnDemand();
    public function setAvailableOnDemand($availableOnDemand);
}
