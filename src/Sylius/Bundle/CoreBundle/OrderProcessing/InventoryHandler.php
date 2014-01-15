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
use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Bundle\InventoryBundle\Factory\InventoryUnitFactoryInterface;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface;

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
        $variants = array();
        $quantities = array();

        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();

            if (!in_array($variant, $variants)) {
                $variants[] = $variant;
            }

            $index = array_search($variant, $variants);
            $quantities[$index] = isset($quantities[$index]) ? $quantities[$index] + $item->getQuantity() : $item->getQuantity();
        }

        foreach ($variants as $variant) {
            $units = $order->getInventoryUnitsByVariant($variant);
            $quantityDifference = $quantities[array_search($variant, $variants)] - count($units);

            if (0 !== $quantityDifference) {
                $quantityDifference < 0 ? $this->removeInventoryUnits($order, $variant, abs($quantityDifference)) : $this->addInventoryUnits($order, $variant, $quantityDifference);
            }
        }

        //remove units of items that no longer exist
        foreach ($order->getInventoryUnits() as $unit) {
            if (!in_array($unit->getStockable(), $variants)) {
                $order->removeInventoryUnit($unit);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateInventory(OrderInterface $order)
    {
        foreach ($units = $order->getInventoryUnits() as $unit) {
            $unit->setInventoryState(InventoryUnitInterface::STATE_SOLD);
        }

        $this->inventoryOperator->decrease($units);
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
        $counter = 0;

        foreach ($units as $unit) {
            if ($counter == $quantity) {
                break;
            }

            $order->removeInventoryUnit($unit);
            $counter++;
        }
    }
}
