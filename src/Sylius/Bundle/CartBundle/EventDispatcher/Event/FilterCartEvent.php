<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventDispatcher\Event;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Filter cart event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterCartEvent extends Event
{
    /**
     * Cart.
     *
     * @var CartInterface
     */
    protected $cart;

    /**
     * Constructor.
     *
     * @param CartInterface $cart
     */
    public function __construct(CartInterface $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Returns cart.
     *
     * @return CartInterface
     */
    public function getCart()
    {
        return $this->cart;
    }
}
