<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Confirmation listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ConfirmationListener
{
    /**
     * Catches placed order.
     * Sets confirmed to false and sends confirmation email to buyer.
     *
     * @param GenericEvent $event
     */
    public function onOrderPlace(GenericEvent $event)
    {
        $order = $event->getSubject();
        $order->setConfirmed(false);
    }
}
