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

use Symfony\Component\EventDispatcher\Event;
use Sylius\Bundle\CartBundle\Model\CartInterface;

/**
 * Filter cart event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterCartEvent extends Event
{
    protected $cart;
    
    public function __construct(CartInterface $cart)
    {
        $this->cart = $cart;
    }
    
    public function getCart()
    {
        return $this->cart;
    }
}