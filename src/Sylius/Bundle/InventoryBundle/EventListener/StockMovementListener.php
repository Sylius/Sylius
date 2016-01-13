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

use Sylius\Component\Inventory\Factory\StockMovementFactoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Creates stock movement for new location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementListener
{
    /**
     * @var StockMovementFactoryInterface
     */
    private $stockMovementFactory;

    /**
     * StockMovementListener constructor.
     */
    public function __construct(StockMovementFactoryInterface $stockMovementFactory)
    {
        $this->stockMovementFactory = $stockMovementFactory;
    }

    /**
     * @param GenericEvent $event
     */
    public function createStockMovememt(GenericEvent $event)
    {
        $stockItem = $event->getSubject();
        $quantity = $event->getArgument('quantity');

        if ($quantity == 0) {
            throw new \InvalidArgumentException('Quantity must be greater or less than 0.');
        }

        $this->stockMovementFactory->createForStockItem($stockItem, $quantity);
    }
}
