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
 * Default stock operator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
class InventoryOperator implements InventoryOperatorInterface
{
    /**
     * Inventory unit manager.
     *
     * @var InventoryUnitManagerInterface
     */
    protected $inventoryUnitManager;

    /**
     * Constructor.
     *
     * @param InventoryUnitManagerInterface $inventoryUnitManager
     */
    public function __construct(InventoryUnitManagerInterface $inventoryUnitManager)
    {
        $this->inventoryUnitManager = $inventoryUnitManager;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(StockableInterface $stockable)
    {
        $onHand = $stockable->getOnHand();
        $currentStock = $this->inventoryUnitManager->countInventoryUnitsBy($stockable, array(
            'state' => InventoryUnitInterface::STATE_AVAILABLE
        ));

        if ($onHand === $currentStock) {
            return;
        } elseif ($onHand > $currentStock) {
            $this->restock($stockable, $onHand - $currentStock);
        } elseif ($onHand < $currentStock) {
            $this->unstock($stockable, $currentStock - $onHand);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function restock(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_AVAILABLE)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units to restock must be greater than 1');
        }

        for ($i = 0; $i < $quantity; $i++) {
            $inventoryUnit = $this->inventoryUnitManager->createInventoryUnit($stockable);
            $inventoryUnit->setState($state);

            $this->inventoryUnitManager->persistInventoryUnit($inventoryUnit);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unstock(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_AVAILABLE)
    {
        $inventoryUnits = $this->inventoryUnitManager->findInventoryUnitsBy(array(
            'stockableId' => $stockable->getStockableId(),
            'state'       => $state
        ));


        for ($i = 0; $i < $quantity; $i++) {
            $this->inventoryUnitManager->removeInventoryUnit($inventoryUnits[$i]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function transfer(StockableInterface $stockable, $quantity = 1, $from = InventoryUnitInterface::STATE_AVAILABLE, $to = InventoryUnitInterface::STATE_UNAVAILABLE)
    {
        $inventoryUnits = $this->inventoryUnitManager->findInventoryUnitsBy(array(
            'stockableId' => $stockable->getStockableId(),
            'state'       => $state
        ));

        $transfered = array();

        for ($i = 0; $i < $quantity; $i++) {
            $inventoryUnit = $inventoryUnits[$i];
            $inventoryUnit->setState($to);

            $this->inventoryUnitManager->persistInventoryUnit($inventoryUnit);
            $transfered[] = $inventoryUnit;
        }

        return $transfered;
    }
}
