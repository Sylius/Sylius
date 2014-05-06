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

/**
 * Stock movement factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockMovementFactoryInterface
{
    /**
     * Create stock movement for given stock item.
     *
     * @param StockItemInterface $stockItem
     * @param integer            $quantity
     */
    public function create(StockItemInterface $stockItem, $quantity);
}
