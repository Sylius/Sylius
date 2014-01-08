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
        list ($variants, $quantities) = $this->getVariantQuantities($order);

        foreach ($variants as $variant) {
            $this->updateVariantUnits($order, $variant, $quantities[array_search($variant, $variants)]);
        }

        $this->removeUnusedUnits($order, $variants);
    }

    /**
     * Removes inventory units which are not linked to any of specified variants
     *
     * @param OrderInterface $order
     * @param array $variants
     */
    private function removeUnusedUnits(OrderInterface $order, array $variants)
    {
        foreach ($order->getInventoryUnits() as $unit) {
            if (!in_array($unit->getStockable(), $variants)) {
                $order->removeInventoryUnit($unit);
            }
        }
    }

    /**
     * Update inventory units related to passed variant to the specified quantity
     *
     * @param OrderInterface $order
     * @param VariantInterface $variant
     * @param int $quantity
     */
    private function updateVariantUnits(OrderInterface $order, VariantInterface $variant, $quantity)
    {
        $units = $order->getInventoryUnitsByVariant($variant);
        $quantityDifference = $quantity - count($units);

        if (0 === $quantityDifference) {
            return;
        }

        if ($quantityDifference < 0) {
            $this->removeInventoryUnits($order, $variant, abs($quantityDifference));
        } else {
            $this->addInventoryUnits($order, $variant, $quantityDifference);
        }
    }

    /**
     * Helper method that returns the quantities of each variant in cart
     * Return format: [[Variant1, Variant2], [QuantityForVariant1, QuantityForVariant2]]
     *
     * @param OrderInterface $order
     * @return array
     */
    private function getVariantQuantities(OrderInterface $order)
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

        return array($variants, $quantities);
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

        foreach ($units->slice(0, $quantity) as $unit) {
            $order->removeInventoryUnit($unit);
        }
    }
}
