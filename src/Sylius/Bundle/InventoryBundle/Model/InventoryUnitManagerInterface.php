<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Model;

/**
 * Inventory unit manager interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface InventoryUnitManagerInterface
{
    /**
     * Creates new inventory unit object.
     *
     * @param StockableInterface $stockable
     * @param integer            $state
     *
     * @return InventoryUnitInterface
     */
    function createInventoryUnit(StockableInterface $stockable, $state);

    /**
     * Persists inventory unit.
     *
     * @param InventoryUnitInterface $inventoryUnit
     */
    function persistInventoryUnit(InventoryUnitInterface $inventoryUnit);

    /**
     * Deletes inventory unit.
     *
     * @param InventoryUnitInterface $inventoryUnit
     */
    function removeInventoryUnit(InventoryUnitInterface $inventoryUnit);

    /**
     * Finds inventory unit by id.
     *
     * @param integer $id
     *
     * @return InventoryUnitInterface
     */
    function findInventoryUnit($id);

    /**
     * Finds inventory unit by criteria.
     *
     * @param array $criteria
     *
     * @return InventoryUnitInterface
     */
    function findInventoryUnitBy(array $criteria);

    /**
     * Finds all inventory units.
     *
     * @return array
     */
    function findInventoryUnits();

    /**
     * Finds inventory units by criteria.
     *
     * @param array $criteria
     *
     * @return array
     */
    function findInventoryUnitsBy(array $criteria);

    /**
     * Get total unavailable units for given stockable.
     *
     * @param array $criteria
     *
     * @return integer
     */
    function countInventoryUnitsBy(array $criteria);

    /**
     * Returns FQCN of inventory unit.
     *
     * @return string
     */
    function getClass();
}
