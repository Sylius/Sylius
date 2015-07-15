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

class OrderUpdateListener
{
    /**
     * @param GenericEvent $event
     */
    public function recalculateOrderTotal(GenericEvent $event)
    {
        $order = $event->getSubject();
        $order->calculateTotal();
    }
}
