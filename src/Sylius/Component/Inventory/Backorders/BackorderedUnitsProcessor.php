<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Backorders;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Inventory\Model\InventoryUnit;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Backordered units processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BackorderedUnitsProcessor implements BackorderedUnitsProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function processBackorders($inventoryUnits)
    {
        if ($inventoryUnits instanceof Collection) {
            if ($inventoryUnits->isEmpty()) {
                return;
            }

            $stockable = $inventoryUnits->first()->getStockable();
        } elseif (is_array($inventoryUnits)) {
            if (empty($inventoryUnits)) {
                return;
            }

            $stockable = $inventoryUnits[0]->getStockable();
        } else {
            throw new \InvalidArgumentException('Inventory units value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        $this->validateInventoryUnits($inventoryUnits);

        $this->processInventoryUnits($inventoryUnits, $stockable, $stockable->getOnHand());
    }

    /**
     * @param InventoryUnit[] $inventoryUnits
     *
     * @throws \InvalidArgumentException
     */
    private function validateInventoryUnits($inventoryUnits)
    {
        foreach ($inventoryUnits as $inventoryUnit) {
            if (!$inventoryUnit instanceof InventoryUnitInterface) {
                throw new \InvalidArgumentException('Only InventoryUnitInterface objects can be processed.');
            }
        }
    }

    /**
     * @param InventoryUnit[]    $inventoryUnits
     * @param StockableInterface $stockable
     * @param int                $onHand
     *
     * @throws \InvalidArgumentException
     */
    private function processInventoryUnits($inventoryUnits, StockableInterface $stockable, $onHand)
    {
        // Backorder units.
        $i = 0;

        foreach ($inventoryUnits as $inventoryUnit) {
            if ($stockable !== $inventoryUnit->getStockable()) {
                throw new \InvalidArgumentException('Do not mix the inventory units when processing backorders.');
            }
            if (++$i > $onHand) {
                $inventoryUnit->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
            }
        }
    }
}
