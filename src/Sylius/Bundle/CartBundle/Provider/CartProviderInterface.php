<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Provider;

use Sylius\Bundle\CartBundle\Model\CartInterface;

/**
 * Interface for object that is accessor for cart.
 * It should retrieve existing cart or create new one based on storage.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartProviderInterface
{
    /**
     * Returns true if a cart exists otherwise false.
     * It does not create a new cart if none exists
     *
     * @return boolean
     */
    public function hasCart();

    /**
     * Returns current cart.
     * If none found is by storage, it should create new one and save it.
     *
     * @return CartInterface
     */
    public function getCart();

    /**
     * Sets given cart as current one.
     * Also should update storage if any is used.
     *
     * @param CartInterface $cart
     */
    public function setCart(CartInterface $cart);

    /**
     * Abandon current cart.
     */
    public function abandonCart();
}
