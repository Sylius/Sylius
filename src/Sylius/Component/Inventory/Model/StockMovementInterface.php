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

/**
 * Stock movement.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockMovementInterface
{
    public function getQuantity();
    public function setQuantity($quantity);

    public function getStockItem();
    public function setStockItem(StockItemInterface $stockItem);

    public function getCreatedAt();
    public function setCreatedAt(\DateTime $createdAt);
}
