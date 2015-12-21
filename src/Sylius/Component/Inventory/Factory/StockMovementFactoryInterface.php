<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Factory;

use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockMovementFactoryInterface extends FactoryInterface
{
    /**
     * Create stock movement for given stock item.
     *
     * @param StockItemInterface $stockItem
     * @param int                $quantity
     */
    public function createForStockItem(StockItemInterface $stockItem, $quantity);
}
