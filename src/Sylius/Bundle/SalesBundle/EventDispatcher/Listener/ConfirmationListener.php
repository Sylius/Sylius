<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\EventDispatcher\Listener;

use Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent;

/**
 * Confirmation listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ConfirmationListener
{
    public function __construct()
    {
    }

    /**
     * Catches placed order.
     * Sets confirmed to false and sends confirmation email to buyer.
     *
     * @param FilterOrderEvent $event
     */
    public function onOrderPlace(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $order->setConfirmed(false);
    }
}
