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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Stock item interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemInterface extends TimestampableInterface, ResourceInterface, StockLocationAwareInterface
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
     * @return int
     */
    public function getOnHold();

    /**
     * @param int
     */
    public function setOnHold($onHold);

    /**
     * @return int
     */
    public function getOnHand();

    /**
     * @param int $onHand
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
