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
 * Backorders processor interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface BackorderedUnitsProcessorInterface
{
    /**
     * Processes given inventory units and marks backorders if any.
     *
     * @param InventoryUnitInterface[]|Collection $inventoryUnits
     */
    public function processBackorders($inventoryUnits);
}
