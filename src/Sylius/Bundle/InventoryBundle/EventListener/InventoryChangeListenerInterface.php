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

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Inventory change listeners must implement this interface because {@link SyliusInventoryExtension}
 * tags events with {@link InventoryChangeListenerInterface::onInventoryChange()} method.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface InventoryChangeListenerInterface
{
    /**
     * Fired on inventory change.
     *
     * @param GenericEvent $event
     */
    public function onInventoryChange(GenericEvent $event);
}
