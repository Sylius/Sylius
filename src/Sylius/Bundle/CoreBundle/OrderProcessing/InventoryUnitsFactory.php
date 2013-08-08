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
     * Constructor.
     *
     * @param InventoryOperatorInterface $inventoryOperator
     */
    public function __construct(InventoryOperatorInterface $inventoryOperator)
    {
        $this->inventoryOperator = $inventoryOperator;
    }

    /**
     * {@inheritdoc}
     */
    public function createInventoryUnits(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            $units = $this->inventoryOperator->decrease($variant, $item->getQuantity());

            foreach ($units as $unit) {
                $order->addInventoryUnit($unit);
            }
        }
    }
}
