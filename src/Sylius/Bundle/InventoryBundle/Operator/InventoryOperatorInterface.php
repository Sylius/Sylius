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
     * Restock inventory units for given stockable, quantity and apply the specified state.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     * @param integer            $state
     */
    function restock(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_AVAILABLE);

    /**
     * Unstock inventory units for given stockable, quantity and apply the specified state.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     * @param integer            $state
     */
    function unstock(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_AVAILABLE);

    /**
     * Transfer units.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     * @param integer            $from
     * @param integer            $to
     *
     * @return array An array or collection of InventoryUnitInterface
     */
    function transfer(StockableInterface $stockable, $quantity, $from = InventoryUnitInterface::STATE_AVAILABLE, $to = InventoryUnitInterface::STATE_UNAVAILABLE);
}
