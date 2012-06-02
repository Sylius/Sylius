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
                $this->fillBackorders($stockable, $onHand, $backorderedUnits);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function increase(StockableInterface $stockable, $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1');
        }

        $stockable->setOnHand($stockable->getOnHand() + $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function decrease(StockableInterface $stockable, $quantity, $state = InventoryUnitInterface::STATE_SOLD)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1');
        }

        $onHand = $stockable->getOnHand();

        if ($quantity > $onHand) {

            return false;
        }

        $stockable->setOnHand($onHand - $quantity);

        return $this->create($stockable, $quantity, $state);
    }

    /**
     * {@inheritdoc}
     */
    public function create(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_AVAILABLE)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1');
        }

        $units = array();

        for ($i = 0; $i < $quantity; $i++) {
            $inventoryUnit = $this->inventoryUnitManager->createInventoryUnit($stockable);
            $inventoryUnit->setState($state);

            $this->inventoryUnitManager->persistInventoryUnit($inventoryUnit);

            $units[] = $inventoryUnit;
        }

        return $units;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(InventoryUnitInterface $inventoryUnit)
    {
        $this->inventoryUnitManager->removeInventoryUnit($inventoryUnit);
    }

    /**
     * Fill backordered units.
     *
     * @param StockableInterface $stockable
     * @param integer            $onHand;
     * @param integer            $backorderedUnits
     */
    protected function fillBackorders(StockableInterface $stockable, $onHand, $backorderedUnits)
    {
        // pufff.
    }
}
