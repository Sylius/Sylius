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

use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;

/**
 * Order inventory handler.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
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
    public function processInventoryUnits(OrderItemInterface $item)
    {
        $nbUnits = $item->getInventoryUnits()->count();

        if ($item->getQuantity() > $nbUnits) {
            $this->createInventoryUnits($item, $item->getQuantity() - $nbUnits);
        } elseif ($item->getQuantity() < $nbUnits) {
            foreach ($item->getInventoryUnits()->slice(0, $nbUnits - $item->getQuantity()) as $unit) {
                $item->removeInventoryUnit($unit);
            }
        }

        foreach ($item->getInventoryUnits() as $unit) {
            if ($unit->getStockable() !== $item->getVariant()) {
                $unit->setStockable($item->getVariant());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function holdInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $quantity = 0;

            foreach ($item->getInventoryUnits() as $unit) {
                if (InventoryUnitInterface::STATE_CHECKOUT === $unit->getInventoryState()) {
                    $unit->setInventoryState(InventoryUnitInterface::STATE_ONHOLD);
                    $quantity++;
                }
            }

            $this->inventoryOperator->hold($item->getVariant(), $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function releaseInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $quantity = 0;

            foreach ($item->getInventoryUnits() as $unit) {
                if (InventoryUnitInterface::STATE_ONHOLD === $unit->getInventoryState()) {
                    $unit->setInventoryState(InventoryUnitInterface::STATE_CHECKOUT);
                    $quantity++;
                }
            }

            $this->inventoryOperator->release($item->getVariant(), $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $units = $item->getInventoryUnits();
            $quantity = 0;

            foreach ($units as $unit) {
                if (in_array($unit->getInventoryState(), array(InventoryUnitInterface::STATE_ONHOLD, InventoryUnitInterface::STATE_CHECKOUT))) {
                    if (InventoryUnitInterface::STATE_ONHOLD === $unit->getInventoryState()) {
                        $quantity++;
                    }

                    $unit->setInventoryState(InventoryUnitInterface::STATE_SOLD);
                }
            }

            $this->inventoryOperator->release($item->getVariant(), $quantity);
            $this->inventoryOperator->decrease($units);
        }
    }

    protected function createInventoryUnits(OrderItemInterface $item, $quantity, $state = InventoryUnitInterface::STATE_CHECKOUT)
    {
        $units = $this->inventoryUnitFactory->create($item->getVariant(), $quantity, $state);

        foreach ($units as $unit) {
            $item->addInventoryUnit($unit);
        }
    }
}
