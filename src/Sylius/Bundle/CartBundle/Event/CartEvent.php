<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Event;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;

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
     * @var Boolean
     */
    protected $isValid = true;

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
     * informations
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

    /**
     * Force the event listeners to check validation of given cart
     *
     * @param null|Boolean $valid
     *
     * @return Boolean
     */
    public function isValid($valid = null)
    {
        if (null === $valid) {
            return $this->isValid;
        }

        return $this->isValid = (Boolean) $valid;
    }
}
