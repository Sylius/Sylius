<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Event;

use Sylius\Component\Cart\Model\CartInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartEvent extends Event
{
    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * @param CartInterface $cart
     */
    public function __construct(CartInterface $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return CartInterface
     */
    public function getCart()
    {
        return $this->cart;
    }
}
