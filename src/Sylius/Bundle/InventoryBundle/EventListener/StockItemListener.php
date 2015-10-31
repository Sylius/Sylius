<?php

/*
 * This file is part of the Sylius sandbox application.
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

    public function __construct(StockItemFactoryInterface $stockItemFactory)
    {
        $this->stockItemFactory = $stockItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAllForStockable(GenericEvent $event)
    {
        $this->stockItemFactory->createAllForStockable($event->getSubject());
    }

    /**
     * {@inheritdoc}
     */
    public function createAllForLocation(GenericEvent $event)
    {
        $this->stockItemFactory->createAllForLocation($event->getSubject());
    }
}
