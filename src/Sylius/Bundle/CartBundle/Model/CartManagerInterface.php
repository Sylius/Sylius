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
 * Interface for cart manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartManagerInterface
{
    /**
     * Creates a new cart instance.
     * 
     * @return CartInterface
     */
    function createCart();
    
    /**
     * Persists cart object.
     * 
     * @param CartInterface $cart
     */
    function persistCart(CartInterface $cart);
    
    /**
     * Removes cart object.
     * 
     * @param CartInterface $cart
     */
    function removeCart(CartInterface $cart);
    
    function flushCarts();
    
    /**
     * Finds cart by id.
     * 
     * @param $id
     * 
     * @return CartInterface|null
     */
    function findCart($id);
    
    /**
     * Finds cart by given criteria.
     * 
     * @param array $criteria
     */
    function findCartBy(array $criteria);
    
    /**
     * Finds all carts.
     * 
     * @return array
     */
    function findCarts();
    
    /**
     * Finds carts by criteria.
     * 
     * @param array $criteria
     */
    function findCartsBy(array $criteria);
    
    /**
     * Returns FQCN of cart model.
     * 
     * @return string
     */
    function getClass();
}
