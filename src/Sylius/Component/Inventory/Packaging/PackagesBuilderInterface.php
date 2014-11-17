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
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Packer is responsible for packaging inventory for every stockable and stock location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PackagesBuilderInterface
{
    /**
     * Build packages for given inventory units.
     *
     * @param InventoryUnitInterface[] $inventoryUnits
     *
     * @return PackageInterface[]
     */
    public function buildPackages(Collection $inventoryUnits);
}
