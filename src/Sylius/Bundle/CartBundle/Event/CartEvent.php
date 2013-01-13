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

use Symfony\Component\EventDispatcher\Event;

use Sylius\Bundle\CartBundle\Model\CartInterface;

class CartEvent extends Event
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
     * @param  null|Boolean $fresh
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
     * @param  null|Boolean $valid
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
