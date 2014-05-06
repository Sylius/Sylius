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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\StockItemInterface;

/**
 * Backorders filler interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface BackordersFillerInterface
{
    /**
     * Fill backordered inventory units when new stock is received.
     *
     * @param StockItemInterface $stockItem
     */
    public function fillBackorders(StockItemInterface $stockItem);
}
