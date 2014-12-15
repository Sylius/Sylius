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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\SyliusStockableEvents;
use Sylius\Component\Product\Model\VariantInterface;
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

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_INCREASE, new GenericEvent($stockItem));

        $stockItem->setOnHand($stockItem->getOnHand() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_INCREASE, new GenericEvent($stockItem));
    }

    /**
     * {@inheritdoc}
     */
    public function hold(ProductVariantInterface $variant, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_HOLD, new GenericEvent($variant));

        $variant->setOnHold($variant->getOnHold() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_HOLD, new GenericEvent($variant));
    }

    /**
     * {@inheritdoc}
     */
    public function release(ProductVariantInterface $variant, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_RELEASE, new GenericEvent($variant));

        $variant->setOnHold($variant->getOnHold() - $quantity);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_RELEASE, new GenericEvent($variant));
    }

    /**
     * {@inheritdoc}
     */
    public function decrease($inventoryUnits)
    {
        if (!is_array($inventoryUnits) && !$inventoryUnits instanceof Collection) {
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

        $onHand = $stockable->getTotalOnHand();

        foreach ($inventoryUnits as $inventoryUnit) {
            if (InventoryUnitInterface::STATE_SOLD === $inventoryUnit->getInventoryState()) {
                --$onHand;
            }
        }

//        $stockable->setOnHand($onHand);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_DECREASE, new GenericEvent($stockable));
    }
}
