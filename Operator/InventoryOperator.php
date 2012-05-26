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
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitManagerInterface;
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
     * Backorders enabled?
     *
     * @var Boolean
     */
    protected $backorders;

    /**
     * Constructor.
     *
     * @param InventoryUnitManagerInterface $inventoryUnitManager
     * @param Boolean                       $backorders
     */
    public function __construct(InventoryUnitManagerInterface $inventoryUnitManager, $backorders = true)
    {
        $this->inventoryUnitManager = $inventoryUnitManager;
        $backorders = (Boolean) $backorders;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(StockableInterface $stockable)
    {
        // If we allow backorders.
        if ($this->backorders) {
            $onHand = $stockable->getOnHand();
            $backorderedUnits = $this->inventoryUnitManager->countInventoryUnitsBy(array(
                'stockableId' => $stockable->getStockableId(),
                'state'       => InventoryUnitInterface::STATE_BACKORDERED
            ));

            if (0 < $onHand && 0 < $backorderedUnits) {
            }
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
            echo "restocking ". $stockable->getStockableId() . " \n";
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
