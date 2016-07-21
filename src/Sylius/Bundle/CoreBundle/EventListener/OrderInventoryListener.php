<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderInventoryListener
{
    /**
     * @var InventoryHandlerInterface
     */
    protected $inventoryHandler;

    /**
     * @var array
     */
    private static $orderStateToInventoryUnitState = [
        OrderInterface::STATE_NEW => InventoryUnitInterface::STATE_ONHOLD,
        OrderInterface::STATE_FULFILLED => InventoryUnitInterface::STATE_SOLD,
    ];

    /**
     * @param InventoryHandlerInterface $inventoryHandler
     */
    public function __construct(InventoryHandlerInterface $inventoryHandler)
    {
        $this->inventoryHandler = $inventoryHandler;
    }

    /**
     * @param GenericEvent $event
     */
    public function holdInventoryUnits(GenericEvent $event)
    {
        $this->inventoryHandler->holdInventory($this->getOrder($event));
    }

    /**
     * @param GenericEvent $event
     */
    public function resolveInventoryState(GenericEvent $event)
    {
        $orderItem = $this->getItem($event);

        $orderState = $orderItem->getOrder()->getState();
        if (!isset(static::$orderStateToInventoryUnitState[$orderState])) {
            return;
        }

        foreach ($orderItem->getUnits() as $itemUnit) {
            $itemUnit->setInventoryState(static::$orderStateToInventoryUnitState[$orderState]);
        }
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderInterface
     */
    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        return $order;
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderItemInterface
     */
    protected function getItem(GenericEvent $event)
    {
        $item = $event->getSubject();

        Assert::isInstanceOf($item, OrderItemInterface::class);

        return $item;
    }
}
