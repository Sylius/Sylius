<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Operator;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\SyliusStockItemEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Default inventory operator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryOperator implements InventoryOperatorInterface
{
    /**
     * Backorders handler.
     *
     * @var BackordersHandlerInterface
     */
    protected $backordersHandler;

    /**
     * Availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    protected $availabilityChecker;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param BackordersHandlerInterface   $backordersHandler
     * @param AvailabilityCheckerInterface $availabilityChecker
     * @param EventDispatcherInterface     $eventDispatcher
     */
    public function __construct(BackordersHandlerInterface $backordersHandler, AvailabilityCheckerInterface $availabilityChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->backordersHandler = $backordersHandler;
        $this->availabilityChecker = $availabilityChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function increase(StockItemInterface $stockItem, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_INCREASE, new GenericEvent($stockItem));

        $stockItem->setOnHand($stockItem->getOnHand() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_INCREASE, new GenericEvent($stockItem));
    }

    /**
     * {@inheritdoc}
     */
    public function hold(StockItemInterface $stockable, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_HOLD, new GenericEvent($stockable));

        $stockable->setOnHold($stockable->getOnHold() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_HOLD, new GenericEvent($stockable));
    }

    /**
     * {@inheritdoc}
     */
    public function release(StockItemInterface $stockable, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_RELEASE, new GenericEvent($stockable));

        $stockable->setOnHold($stockable->getOnHold() - $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_RELEASE, new GenericEvent($stockable));
    }

    /**
     * {@inheritdoc}
     */
    public function decrease($inventoryUnits)
    {
        //TODO review this
        throw new \Exception('NOT IMPLEMENTED YET');
        /*if (!is_array($inventoryUnits) && !$inventoryUnits instanceof Collection) {
            throw new \InvalidArgumentException('Inventory units value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        $quantity = count($inventoryUnits);

        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        if ($inventoryUnits instanceof Collection) {
            $stockable = $inventoryUnits->first()->getStockable();
        } else {
            $stockable = $inventoryUnits[0]->getStockable();
        }

        if (!$this->availabilityChecker->isStockSufficient($stockable, $quantity)) {
            throw new InsufficientStockException($stockable, $quantity);
        }

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_DECREASE, new GenericEvent($stockable));

        $this->backordersHandler->processBackorders($inventoryUnits);

        $onHand = $stockable->getOnHand();

        foreach ($inventoryUnits as $inventoryUnit) {
            if (InventoryUnitInterface::STATE_SOLD === $inventoryUnit->getInventoryState()) {
                --$onHand;
            }
        }

        $stockable->setOnHand($onHand);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_DECREASE, new GenericEvent($stockable));*/
    }
}
