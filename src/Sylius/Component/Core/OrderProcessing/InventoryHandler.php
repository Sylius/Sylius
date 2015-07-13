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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface;
use Sylius\Component\Inventory\InventoryUnitTransitions;
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
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param InventoryOperatorInterface    $inventoryOperator
     * @param InventoryUnitFactoryInterface $inventoryUnitFactory
     * @param FactoryInterface              $factory
     */
    public function __construct(
        InventoryOperatorInterface $inventoryOperator,
        InventoryUnitFactoryInterface $inventoryUnitFactory,
        FactoryInterface $factory
    ) {
        $this->inventoryOperator    = $inventoryOperator;
        $this->inventoryUnitFactory = $inventoryUnitFactory;
        $this->factory              = $factory;
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
            $quantity = $this->applyTransition($item->getInventoryUnits(), InventoryUnitTransitions::SYLIUS_HOLD);

            $this->inventoryOperator->hold($item->getVariant(), $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function releaseInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $quantity = $this->applyTransition($item->getInventoryUnits(), InventoryUnitTransitions::SYLIUS_RELEASE);

            $this->inventoryOperator->release($item->getVariant(), $quantity);
        }
    }

    public function shipShipment(ShipmentInterface $shipment)
    {
        /** @var array $shippedItem An array of ArrayCollection that store InventoryUnit */
        $shippedCollection = [];

        foreach ($shipment->getItems() as $unit) {
            $stateMachine = $this->factory->get($unit, InventoryUnitTransitions::GRAPH);
            $variant = $unit->getOrderItem()->getVariant();

            if ($stateMachine->can(InventoryUnitTransitions::SYLIUS_SELL)) {
                // Release on hold item if possible
                if ($stateMachine->can(InventoryUnitTransitions::SYLIUS_RELEASE)) {
                    $this->inventoryOperator->release($variant, 1);
                }

                // Sell & decrease stock
                $stateMachine->apply(InventoryUnitTransitions::SYLIUS_SELL);

                // Group units by variant, so it decrease stocks all together later
                if (!isset($shippedCollection[$variant->getId()])) {
                    $shippedCollection[$variant->getId()] = new ArrayCollection();
                }

                $shippedCollection[$variant->getId()]->add($unit);
            }
        }

        foreach ($shippedCollection as $collection) {
            $this->inventoryOperator->decrease($collection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateInventory(OrderInterface $order)
    {
        foreach ($order->getShipments() as $shipment) {
            $this->shipShipment($shipment);
        }
    }

    protected function createInventoryUnits(OrderItemInterface $item, $quantity, $state = InventoryUnitInterface::STATE_CHECKOUT)
    {
        $units = $this->inventoryUnitFactory->create($item->getVariant(), $quantity, $state);

        foreach ($units as $unit) {
            $item->addInventoryUnit($unit);
        }
    }

    /**
     * Apply and count a transition on all given units
     *
     * @param Collection $units
     * @param string     $transition
     *
     * @return int
     */
    protected function applyTransition(Collection $units, $transition)
    {
        $quantity = 0;

        foreach ($units as $unit) {
            $stateMachine = $this->factory->get($unit, InventoryUnitTransitions::GRAPH);
            if ($stateMachine->can($transition)) {
                $stateMachine->apply($transition);
                $quantity++;
            }
        }

        return $quantity;
    }
}
