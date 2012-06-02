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

use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Stock operator interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
interface InventoryOperatorInterface
{
    /**
     * Update inventory units.
     *
     * @param StockableInterface $stockable
     */
    function refresh(StockableInterface $stockable);

    /**
     * Increase stock on hand for given stockable by quantity.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     */
    function increase(StockableInterface $stockable, $quantity);

    /**
     * Decrease stock on hand for given stockable by quantity, return units in state if any is passed as third argument.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     * @param integer            $returnUnitsInState
     *
     * @return null|array
     */
    function decrease(StockableInterface $stockable, $quantity, $returnUnitsInState = false);

    /**
     * Create inventory units for given stockable, quantity and apply the specified state.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     * @param integer            $state
     *
     * @return array An array of InventoryUnitInterface objects
     */
    function create(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_SOLD);

    /**
     * Destroy inventory unit.
     *
     * @param InventoryUnitInterface $inventoryUnit
     */
    function destroy(InventoryUnitInterface $inventoryUnit);
}
