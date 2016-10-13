<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Operator;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface InventoryOperatorInterface
{
    /**
     * @param StockableInterface $stockable
     * @param int $quantity
     */
    public function hold(StockableInterface $stockable, $quantity);

    /**
     * @param StockableInterface $stockable
     * @param int $quantity
     */
    public function release(StockableInterface $stockable, $quantity);
}
