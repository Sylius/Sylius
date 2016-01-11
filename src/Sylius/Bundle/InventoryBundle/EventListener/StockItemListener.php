<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\EventListener;

use Sylius\Component\Inventory\Factory\StockItemFactoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Creates stock items for new location or stockable.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItemListener
{
    /**
     * @var StockItemFactoryInterface
     */
    private $stockItemFactory;

    /**
     * @param StockItemFactoryInterface $stockItemFactory
     */
    public function __construct(StockItemFactoryInterface $stockItemFactory)
    {
        $this->stockItemFactory = $stockItemFactory;
    }

    /**
     * @param GenericEvent $event
     */
    public function createAllForStockable(GenericEvent $event)
    {
        $this->stockItemFactory->createAllForStockable($event->getSubject());
    }

    /**
     * @param GenericEvent $event
     */
    public function createAllForLocation(GenericEvent $event)
    {
        $this->stockItemFactory->createAllForLocation($event->getSubject());
    }
}
