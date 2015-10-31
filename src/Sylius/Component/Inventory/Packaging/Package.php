<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Packaging;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;

/**
 * Package model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Package implements PackageInterface
{
    /**
     * @var StockLocationInterface
     */
    private $stockLocation;

    /**
     * @var InventoryUnitInterface[]
     */
    private $inventoryUnits;

    /**
     * @param StockLocationInterface $stockLocation
     */
    public function __construct(StockLocationInterface $stockLocation)
    {
        $this->stockLocation = $stockLocation;
        $this->inventoryUnits = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getStockLocation()
    {
        return $this->stockLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->inventoryUnits->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnits()
    {
        return $this->inventoryUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function addInventoryUnit(InventoryUnitInterface $unit)
    {
        $this->inventoryUnits->add($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function removeInventoryUnit(InventoryUnitInterface $unit)
    {
        $this->inventoryUnits->removeElement($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function hasInventoryUnit(InventoryUnitInterface $unit)
    {
        return $this->inventoryUnits->contains($unit);
    }
}
