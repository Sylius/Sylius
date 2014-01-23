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

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\InventoryHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order inventory processing listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderInventoryListener
{
    /**
     * Order  processor.
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
     * Get the order from event and run the inventory processor on it.
     *
     * @param GenericEvent $event
     */
    public function updateInventoryUnits(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order inventory listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->inventoryHandler->updateInventory($order);
    }

    /**
     * Update the inventory units.
     *
     * @param GenericEvent $event
     */
    public function createInventoryUnits(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order inventory listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->inventoryHandler->processInventoryUnits($order);
    }
}
