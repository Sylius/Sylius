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

use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Inventory change listener.
 * Fills backorders on inventory change.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryChangeListener implements InventoryChangeListenerInterface
{
    /**
     * backorders handler.
     *
     * @var BackordersHandlerInterface
     */
    private $backordersHandler;

    /**
     * Constructor.
     *
     * @param BackordersHandlerInterface $backordersHandler
     */
    public function __construct(BackordersHandlerInterface $backordersHandler)
    {
        $this->backordersHandler = $backordersHandler;
    }

    /**
     * {@inheritdoc}
     *
     * Fills backorders using backorders handler.
     *
     * @param GenericEvent $event
     */
    public function onInventoryChange(GenericEvent $event)
    {
        $this->backordersHandler->fillBackorders($event->getSubject());
    }
}
