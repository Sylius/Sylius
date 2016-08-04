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
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\SyliusStockableEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class InventoryOperator implements InventoryOperatorInterface
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param AvailabilityCheckerInterface $availabilityChecker
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(AvailabilityCheckerInterface $availabilityChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->availabilityChecker = $availabilityChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function increase(StockableInterface $stockable, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_INCREASE, new GenericEvent($stockable));

        $stockable->setOnHand($stockable->getOnHand() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_INCREASE, new GenericEvent($stockable));
    }

    /**
     * {@inheritdoc}
     */
    public function hold(StockableInterface $stockable, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_HOLD, new GenericEvent($stockable));

        $stockable->setOnHold($stockable->getOnHold() + $quantity);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_HOLD, new GenericEvent($stockable));
    }

    /**
     * {@inheritdoc}
     */
    public function release(StockableInterface $stockable, $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 0.');
        }

        $this->eventDispatcher->dispatch(SyliusStockableEvents::PRE_RELEASE, new GenericEvent($stockable));

        $stockable->setOnHold($stockable->getOnHold() - $quantity);

        $this->eventDispatcher->dispatch(SyliusStockableEvents::POST_RELEASE, new GenericEvent($stockable));
    }
}
