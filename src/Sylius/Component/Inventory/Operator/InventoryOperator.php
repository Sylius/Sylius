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

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\SyliusStockableEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class InventoryOperator implements InventoryOperatorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function hold(StockableInterface $stockable, $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity of units must be greater than 0.');

        $this->dispatchEvent(SyliusStockableEvents::PRE_HOLD, $stockable);

        $stockable->setOnHold($stockable->getOnHold() + $quantity);

        $this->dispatchEvent(SyliusStockableEvents::POST_HOLD, $stockable);
    }

    /**
     * {@inheritdoc}
     */
    public function release(StockableInterface $stockable, $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity of units must be greater than 0.');

        $this->dispatchEvent(SyliusStockableEvents::PRE_RELEASE, $stockable);

        $stockable->setOnHold($stockable->getOnHold() - $quantity);

        $this->dispatchEvent(SyliusStockableEvents::POST_RELEASE, $stockable);
    }

    /**
     * @param string $event
     * @param StockableInterface $stockable
     */
    private function dispatchEvent($event, StockableInterface $stockable)
    {
        $this->eventDispatcher->dispatch($event, new GenericEvent($stockable));
    }
}
