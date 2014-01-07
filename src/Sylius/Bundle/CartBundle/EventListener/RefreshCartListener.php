<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Ensure that the cart is refreshed before other listeners.
 */
class RefreshCartListener
{
    public function onCartChange(GenericEvent $event)
    {
        $event->getSubject()->calculateTotal();
    }
}
