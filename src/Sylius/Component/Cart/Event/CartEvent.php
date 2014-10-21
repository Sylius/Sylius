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
use Sylius\Component\Resource\Event\ResourceEvent;

/**
 * Cart event.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartEvent extends ResourceEvent
{
    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * @var Boolean
     */
    protected $isFresh = false;

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

    /**
     * Notice the event listeners to refresh/recalculate cart
     * information's
     *
     * @param null|Boolean $fresh
     *
     * @return Boolean
     */
    public function isFresh($fresh = null)
    {
        if (null === $fresh) {
            return $this->isFresh;
        }

        return $this->isFresh = (Boolean) $fresh;
    }
}
