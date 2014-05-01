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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order inventory processing listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderInventoryListener
{
    /**
     * Inventory handler.
     *
     * @var InventoryHandlerInterface
     */
    protected $inventoryHandler;

    /**
     * Constructor.
     *
     * @param InventoryHandlerInterface $inventoryHandler
     */
    public function __construct(InventoryHandlerInterface $inventoryHandler)
    {
        $this->inventoryHandler = $inventoryHandler;
    }

    /**
     * Put order inventory on hold.
     *
     * @param GenericEvent $event
     */
    public function holdInventoryUnits(GenericEvent $event)
    {
        $this->inventoryHandler->holdInventory(
            $this->getOrder($event)
        );
    }

    /**
     * Release order inventory.
     *
     * @param GenericEvent $event
     */
    public function releaseInventoryUnits(GenericEvent $event)
    {
        $this->inventoryHandler->releaseInventory(
            $this->getOrder($event)
        );
    }

    /**
     * Update the inventory units.
     *
     * @param GenericEvent $event
     */
    public function processInventoryUnits(GenericEvent $event)
    {
        $this->inventoryHandler->processInventoryUnits(
            $this->getItem($event)
        );
    }

    /**
     * Gets order from event.
     *
     * @param GenericEvent $event
     *
     * @return OrderInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        return $order;
    }

    /**
     * Gets order from event.
     *
     * @param GenericEvent $event
     *
     * @return OrderInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function getItem(GenericEvent $event)
    {
        $item = $event->getSubject();

        if (!$item instanceof OrderItemInterface) {
            throw new UnexpectedTypeException(
                $item,
                'Sylius\Component\Core\Model\OrderItemInterface'
            );
        }

        return $item;
    }
}
