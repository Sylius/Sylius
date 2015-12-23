<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * Default inventory operator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnitFactory extends Factory implements InventoryUnitFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createForStockable(StockableInterface $stockable, $quantity, $state = InventoryUnitInterface::STATE_SOLD)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1.');
        }

        $units = new ArrayCollection();

        for ($i = 0; $i < $quantity; $i++) {
            $inventoryUnit = $this->createNew();
            $inventoryUnit->setInventoryState($state);

            $units->add($inventoryUnit);
        }

        return $units;
    }
}
