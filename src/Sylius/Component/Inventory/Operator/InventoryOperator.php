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
use Sylius\Component\Inventory\Factory\StockMovementFactoryInterface;
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
     * @var StockMovementFactoryInterface
     */
    protected $stockMovementFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param StockMovementFactoryInterface $stockMovementFactory
     * @param EventDispatcherInterface      $eventDispatcher
     */
    public function __construct(StockMovementFactoryInterface $stockMovementFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->stockMovementFactory = $stockMovementFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function increase(StockItemInterface $stockItem, $quantity)
    {
        $this->assertQuantityGreaterThanZero($quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_INCREASE, new GenericEvent($stockItem));

        $stockMovement = $this->stockMovementFactory->create($stockItem, $quantity);
        $stockItem->setOnHand($stockItem->getOnHand() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_INCREASE, new GenericEvent($stockItem));
    }

    /**
     * {@inheritdoc}
     */
    public function hold(StockItemInterface $stockItem, $quantity)
    {
        $this->assertQuantityGreaterThanZero($quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_HOLD, new GenericEvent($stockItem));

        $stockItem->setOnHold($stockItem->getOnHold() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_HOLD, new GenericEvent($stockItem));
    }

    /**
     * {@inheritdoc}
     */
    public function release(StockItemInterface $stockItem, $quantity)
    {
        $this->assertQuantityGreaterThanZero($quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_RELEASE, new GenericEvent($stockItem));

        $stockItem->setOnHold($stockItem->getOnHold() - $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_RELEASE, new GenericEvent($stockItem));
    }

    /**
     * {@inheritdoc}
     */
    public function decrease(StockItemInterface $stockItem, $quantity)
    {
        $this->assertQuantityGreaterThanZero($quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::PRE_DECREASE, new GenericEvent($stockItem));

        $stockMovement = $this->stockMovementFactory->create($stockItem, -1 * $quantity);
        $stockItem->setOnHand($stockItem->getOnHand() - $quantity);

        $this->eventDispatcher->dispatch(SyliusStockItemEvents::POST_DECREASE, new GenericEvent($stockItem));
    }

    private function assertQuantityGreaterThanZero($quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }
    }
}
