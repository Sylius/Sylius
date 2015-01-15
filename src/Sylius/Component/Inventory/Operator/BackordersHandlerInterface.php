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
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Backorders handler interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface BackordersHandlerInterface
{
    /**
     * Processes given inventory units and marks backorders if any.
     *
     * @param StockableInterface[]|Collection $inventoryUnits
     */
    public function processBackorders($inventoryUnits);

    /**
     * Update backordered inventory units if quantity is sufficient.
     *
     * @param StockableInterface $stockable
     */
    public function fillBackorders(StockableInterface $stockable);
}
