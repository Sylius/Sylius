<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Operator;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Stock operator interface.
 * Manage stock levels and inventory units.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
interface InventoryOperatorInterface
{
    /**
     * Increase stock on hand for given stockable by quantity.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     */
    public function increase(StockableInterface $stockable, $quantity);

    /**
     * Decrease stock by count of given inventory units.
     *
     * @param array|Collection $inventoryUnits
     */
    public function decrease($inventoryUnits);
}
