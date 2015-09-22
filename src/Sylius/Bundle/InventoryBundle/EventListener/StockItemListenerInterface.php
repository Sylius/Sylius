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

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemListenerInterface
{
    /**
     * Fired on the creation of new stockable.
     *
     * @param GenericEvent $event
     */
    public function onStockableCreate(GenericEvent $event);

    /**
     * Fired on the creation of new stock location.
     *
     * @param GenericEvent $event
     */
    public function onStockLocationCreate(GenericEvent $event);
}
