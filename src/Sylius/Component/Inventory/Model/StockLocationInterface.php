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
 * Stock location interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockLocationInterface extends TimestampableInterface
{
    public function getCode();
    public function setCode($code);

    public function getName();
    public function setName($name);

    public function getStockItems();
    public function addStockItem(StockItemInterface $movement);
    public function removeStockItem(StockItemInterface $movement);
    public function hasStockItem(StockItemInterface $movement);

    public function getStockMovements();
    public function addStockMovement(StockMovementInterface $movement);
    public function removeStockMovement(StockMovementInterface $movement);
    public function hasStockMovement(StockMovementInterface $movement);
}
