<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\VariantInterface;
use Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;

/**
 * Order inventory handler.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryHandler implements InventoryHandlerInterface
{
    /**
     * Inventory operator.
     *
     * @var InventoryOperatorInterface
     */
    protected $inventoryOperator;

    /**
     * Inventory unit factory.
     *
     * @var InventoryUnitFactoryInterface
     */
    protected $inventoryUnitFactory;

    /**
     * Constructor.
     *
     * @param InventoryOperatorInterface    $inventoryOperator
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
    public function processInventoryUnits(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            $units = $order->getInventoryUnitsByVariant($variant);

            $quantity = $item->getQuantity();
            $unitsQuantity = count($units);

            if ($quantity > $unitsQuantity) {
                $this->addInventoryUnits($order, $variant, $quantity - $unitsQuantity);
            }
            if ($quantity < $unitsQuantity) {
                $this->removeInventoryUnits($order, $variant, $unitsQuantity - $quantity);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $units = $order->getInventoryUnitsByVariant($item->getVariant());

            foreach ($units as $unit) {
                $unit->setInventoryState(InventoryUnitInterface::STATE_SOLD);
            }

            $this->inventoryOperator->decrease($units);
        }
    }

    /**
     * Add inventory units to order.
     *
     * @param OrderInterface   $order
     * @param VariantInterface $variant
     * @param integer          $quantity
     */
    protected function addInventoryUnits(OrderInterface $order, VariantInterface $variant, $quantity)
    {
        $units = $this->inventoryUnitFactory->create($variant, $quantity, InventoryUnitInterface::STATE_CHECKOUT);

        foreach ($units as $unit) {
            $order->addInventoryUnit($unit);
        }
    }

    /**
     * Remove inventory units from order.
     *
     * @param OrderInterface   $order
     * @param VariantInterface $variant
     * @param integer          $quantity
     */
    protected function removeInventoryUnits(OrderInterface $order, VariantInterface $variant, $quantity)
    {
        $units = $order->getInventoryUnitsByVariant($variant);

        for ($i = 0; $i < $quantity; $i++) {
            $order->removeInventoryUnit($units[$i]);
        }
    }
}
