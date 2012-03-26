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
    /**
     * Returns cart id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Returns associated cart.
     *
     * @return CartInterface
     */
    function getCart();

    /**
     * Sets cart.
     *
     * @param CartInterface
     */
    function setCart(CartInterface $cart = null);

    /**
     * Returns quantity.
     *
     * @return integer
     */
    function getQuantity();

    /**
     * Sets quantity.
     *
     * @param $quantity
     */
    function setQuantity($quantity);
}
