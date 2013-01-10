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
use Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface;

/**
 * Inventory change listener.
 * Fills backorders on inventory change.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryChangeListener implements InventoryChangeListenerInterface
{
    /**
     * Inventory operator.
     *
     * @var InventoryOperatorInterface
     */
    private $inventoryOperator;

    /**
     * Constructor.
     *
     * @param InventoryOperatorInterface $inventoryOperator
     */
    public function __construct(InventoryOperatorInterface $inventoryOperator)
    {
        $this->inventoryOperator = $inventoryOperator;
    }

    /**
     * {@inheritdoc}
     *
     * Fills backorders using inventory operator.
     *
     * @param GenericEvent $event
     */
    public function onInventoryChange(GenericEvent $event)
    {
        $this->inventoryOperator->fillBackorders($event->getSubject());
    }
}
