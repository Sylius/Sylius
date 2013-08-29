<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Factory;

use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Inventory unit factory interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
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
    public function create(StockableInterface $stockable, $quantity, $state = InventoryUnitInterface::STATE_SOLD);
}
