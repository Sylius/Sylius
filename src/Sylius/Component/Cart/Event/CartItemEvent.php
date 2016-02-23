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
use Sylius\Component\Cart\Model\CartItemInterface;

/**
 * Cart item event.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartItemEvent extends CartEvent
{
    /**
     * @var CartItemInterface
     */
    private $item;

    /**
     * @param CartInterface     $cart
     * @param CartItemInterface $item
     */
    public function __construct(CartInterface $cart, CartItemInterface $item)
    {
        parent::__construct($cart);

        $this->item = $item;
        $this->subject = $item;
    }

    /**
     * @return CartItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}
