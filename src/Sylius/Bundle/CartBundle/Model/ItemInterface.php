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
     * Returns item id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Sets item id.
     *
     * @param mixed $id
     */
    function setId($id);

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

    /**
     * Increment quantity by given amount.
     * By 1 as default.
     *
     * @param integer $quantity
     */
    function incrementQuantity($amount = 1);

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overriden to sum up quantity.
     *
     * @return Boolean
     */
    function equals(ItemInterface $item);
}
