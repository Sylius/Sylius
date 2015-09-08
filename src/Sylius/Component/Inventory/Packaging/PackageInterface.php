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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Package representing a collection of inventory units from particular location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PackageInterface
{
    public function getStockLocation();

    public function isEmpty();
    public function getInventoryUnits();
    public function addInventoryUnit(InventoryUnitInterface $unit);
    public function removeInventoryUnit(InventoryUnitInterface $unit);
    public function hasInventoryUnit(InventoryUnitInterface $unit);
}
