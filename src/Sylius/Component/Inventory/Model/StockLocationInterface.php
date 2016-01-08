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
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * Stock location interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockLocationInterface extends TimestampableInterface, CodeAwareInterface, ToggleableInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return Collection|StockItemInterface[]
     */
    public function getStockItems();

    /**
     * @param StockItemInterface $stockItem
     */
    public function addStockItem(StockItemInterface $stockItem);

    /**
     * @param StockItemInterface $stockItem
     */
    public function removeStockItem(StockItemInterface $stockItem);

    /**
     * @param StockItemInterface $stockItem
     *
     * @return bool
     */
    public function hasStockItem(StockItemInterface $stockItem);

    /**
     * @return Collection|StockMovementInterface[]
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
     *
     * @return bool
     */
    public function hasStockMovement(StockMovementInterface $movement);
}
