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
    	// array(variantId => inventory units quantity)
        $quantities = array();

        // array(variantId => variant)
        $variants = array();

        // We first iterate over cart items, counting items in positive
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();

            if (!isset($units[$variant->getId()])) {
                $quantities[$variant->getId()] = $item->getQuantity();
                $variants[$variant->getId()] = $variant;
            } else {
                $quantities[$variant->getId()] += $item->getQuantity();
            }
        }

        // We then iterate over inventory units to count them in negative
        foreach ($order->getInventoryUnits() as $unit) {
            $variant = $unit->getStockable();

            if (!isset($quantities[$variant->getId()])) {
                $quantities[$variant->getId()] = -1;
                $variants[$variant->getId()] = $variant;
            } else {
            	$quantities[$variant->getId()]--;
            }
        }

        // Finaly, we actually add/remove the inventory units
        foreach ($quantities as $variantId => $quantity) {
            $variant = $variants[$variantId];

            if ($quantity > 0) {
                $this->addInventoryUnits($order, $variant, $quantity);
            } elseif ($quantity < 0) {
                $this->removeInventoryUnits($order, $variant, -$quantity);
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
        $units = $order->getInventoryUnitsByVariant($variant)->slice(0, $quantity);

        foreach ($units as $unit) {
            $order->removeInventoryUnit($unit);
        }
    }
}
