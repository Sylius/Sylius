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
use SM\Factory\FactoryInterface as StateMachineFactoryInteraface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Inventory\InventoryUnitTransitions;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryHandler implements InventoryHandlerInterface
{
    /**
     * @var InventoryOperatorInterface
     */
    protected $inventoryOperator;

    /**
     * @var FactoryInterface
     */
    protected $orderItemUnitFactory;

    /**
     * @var StateMachineFactoryInteraface
     */
    protected $stateMachineFactory;

    /**
     * @param InventoryOperatorInterface $inventoryOperator
     * @param FactoryInterface $orderItemUnitFactory
     * @param StateMachineFactoryInteraface $stateMachineFactory
     */
    public function __construct(
        InventoryOperatorInterface $inventoryOperator,
        FactoryInterface $orderItemUnitFactory,
        StateMachineFactoryInteraface $stateMachineFactory
    ) {
        $this->inventoryOperator    = $inventoryOperator;
        $this->orderItemUnitFactory = $orderItemUnitFactory;
        $this->stateMachineFactory  = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function processInventoryUnits(OrderItemInterface $item)
    {
        $nbUnits = $item->getItemUnits()->count();

        if ($item->getQuantity() > $nbUnits) {
            $this->createInventoryUnits($item, $item->getQuantity() - $nbUnits);
        } elseif ($item->getQuantity() < $nbUnits) {
            foreach ($item->getItemUnits()->slice(0, $nbUnits - $item->getQuantity()) as $unit) {
                $item->removeItemUnit($unit);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function holdInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $quantity = $this->applyTransition($item->getItemUnits(), InventoryUnitTransitions::SYLIUS_HOLD);

            $this->inventoryOperator->hold($item->getVariant(), $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function releaseInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $quantity = $this->applyTransition($item->getItemUnits(), InventoryUnitTransitions::SYLIUS_RELEASE);

            $this->inventoryOperator->release($item->getVariant(), $quantity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $units = $item->getItemUnits();
            $quantity = 0;

            foreach ($units as $unit) {
                $stateMachine = $this->stateMachineFactory->get($unit, InventoryUnitTransitions::GRAPH);
                if ($stateMachine->can(InventoryUnitTransitions::SYLIUS_SELL)) {
                    if ($stateMachine->can(InventoryUnitTransitions::SYLIUS_RELEASE)) {
                        $quantity++;
                    }
                    $stateMachine->apply(InventoryUnitTransitions::SYLIUS_SELL);
                }
            }

            $this->inventoryOperator->release($item->getVariant(), $quantity);
            $this->inventoryOperator->decrease($units);
        }
    }

    /**
     * @param OrderItemInterface $item
     * @param int $quantity
     * @param string $state
     */
    protected function createInventoryUnits(OrderItemInterface $item, $quantity, $state = InventoryUnitInterface::STATE_CHECKOUT)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1.');
        }

        for ($i = 0; $i < $quantity; $i++) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitFactory->createNew();
            $orderItemUnit->setInventoryState($state);

            $item->addItemUnit($orderItemUnit);
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
            $stateMachine = $this->stateMachineFactory->get($unit, InventoryUnitTransitions::GRAPH);
            if ($stateMachine->can($transition)) {
                $stateMachine->apply($transition);
                $quantity++;
            }
        }

        return $quantity;
    }
}
