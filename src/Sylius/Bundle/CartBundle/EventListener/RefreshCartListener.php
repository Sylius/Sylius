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

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Ensure that the cart is refreshed before other listeners.
 */
class RefreshCartListener
{
    /**
     * @var CartProviderInterface
     */
    protected $cartProvider;

    public function __construct(CartProviderInterface $cartProvider)
    {
        $this->cartProvider = $cartProvider;
    }

    public function refreshCart(Event $event)
    {
        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof CartInterface) {
            throw new \InvalidArgumentException(
                'RefreshCartListener requires event subject to be instance of "Sylius\Component\Cart\Model\CartInterface"'
            );
        }

        var_dump('refreshing cart!');
        dump('refreshing cart!');
        $cart->calculateTotal();
    }
}
