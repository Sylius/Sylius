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
use Sylius\Bundle\CoreBundle\OrderProcessing\InventoryUnitsFactoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order taxation listener.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderInventoryUnitsListener
{
    /**
     * Shipment Factory
     *
     * @var InventoryUnitsFactoryInterface
     */
    protected $inventoryUnitsFactory;

    /**
     * Constructor.
     *
     * @param InventoryUnitsFactoryInterface $inventoryUnitsFactory
     */
    public function __construct(InventoryUnitsFactoryInterface $inventoryUnitsFactory)
    {
        $this->inventoryUnitsFactory = $inventoryUnitsFactory;
    }

    /**
     * Get the order from event and create inventory units.
     *
     * @param GenericEvent $event
     */
    public function createOrderInventoryUnits(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order inventoryUnits listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->inventoryUnitsFactory->createInventoryUnits($order);
    }
}
