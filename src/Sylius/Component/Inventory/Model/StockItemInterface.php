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
     * @return StockableInterface
     */
    public function getStockable();

    /**
     * @param $stockable StockableInterface
     */
    public function setStockable(StockableInterface $stockable);

    /**
     * @return StockLocationInterface
     */
    public function getLocation();

    /**
     * @param StockLocationInterface
     */
    public function setLocation(StockLocationInterface $location);

    /**
     * @return StockMovementInterface[]
     */
    public function getStockMovements();

    /**
     * @param StockMovementInterface $movement
     */
    public function addStockMovement(StockMovementInterface $movement);

    /**
     * @param StockMovementInterface $movement
     */
    public function removeStockMovement(StockMovementInterface $movement);

    /**
     * @param StockMovementInterface $movement
     */
    public function hasStockMovement(StockMovementInterface $movement);

    /**
     * @return integer
     */
    public function getOnHold();

    /**
     * @param integer
     */
    public function setOnHold($onHold);

    /**
     * @return integer
     */
    public function getOnHand();

    /**
     * @param integer $onHand
     */
    public function setOnHand($onHand);

    /**
     * @return bool
     */
    public function isAvailableOnDemand();

    /**
     * @param bool
     */
    public function setAvailableOnDemand($availableOnDemand);
}
