<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Model;

/**
 * Interface for cart item model.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemInterface
{
    function getId();
    function getCart();
    function setCart(CartInterface $cart = null);
    function getQuantity();
    function setQuantity($quantity);
}
