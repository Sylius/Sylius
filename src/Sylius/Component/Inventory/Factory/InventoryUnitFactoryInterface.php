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

use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;

/**
 * Inventory unit factory interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface InventoryUnitFactoryInterface
{
    /**
     * Create given amount of inventory units.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     * @param string             $state
     */
    public function createForStockable(StockableInterface $stockable, $quantity, $state = InventoryUnitInterface::STATE_SOLD);
}
