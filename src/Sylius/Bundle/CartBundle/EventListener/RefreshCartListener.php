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

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Ensure that the cart is refreshed before other listeners.
 */
class RefreshCartListener
{
    public function refreshCart(GenericEvent $event)
    {
        $cart = $event->getSubject();

        if (!$cart instanceof CartInterface) {
            throw new \InvalidArgumentException(
                'RefreshCartListener requires event subject to be instance of "Sylius\Bundle\CartBundle\Model\CartInterface"'
            );
        }

        $cart->calculateTotal();
    }
}
