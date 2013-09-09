<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface;
use Sylius\Bundle\InventoryBundle\Factory\InventoryUnitFactoryInterface;

/**
 * Inventory units factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnitsFactory implements InventoryUnitsFactoryInterface
{
    /**
     * Inventory operator.
     *
     * @var InventoryOperatorInterface
     */
    protected $inventoryOperator;

    /**
     * Inventory unit factory
     *
     * @var InventoryUnitFactoryInterface
     */
    protected $inventoryUnitFactory;

    /**
     * Constructor.
     *
     * @param InventoryOperatorInterface $inventoryOperator
     * @param InventoryUnitFactoryInterface $inventoryUnitFactory
     */
    public function __construct(InventoryOperatorInterface $inventoryOperator, InventoryUnitFactoryInterface $inventoryUnitFactory)
    {
        $this->inventoryOperator = $inventoryOperator;
        $this->inventoryUnitFactory = $inventoryUnitFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createInventoryUnits(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            $units = $this->inventoryUnitFactory->create($variant, $item->getQuantity());

            $this->inventoryOperator->decrease($units->toArray());

            foreach ($units as $unit) {
                $order->addInventoryUnit($unit);
            }
        }
    }
}
