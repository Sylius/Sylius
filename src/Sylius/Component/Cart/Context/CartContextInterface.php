<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Context;

use Sylius\Component\Cart\Model\CartInterface;

/**
 * Interface to be implemented by the service providing the currently used
 * cart.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartContextInterface
{
    // Key used to store the cart in storage.
    const STORAGE_KEY = '_sylius_cart_id';

    /**
     * Get the currently active cart.
     *
     * @return string
     */
    public function getCurrentCartIdentifier();

    /**
     * Set the currently active cart.
     *
     * @param CartInterface $cart
     */
    public function setCurrentCartIdentifier(CartInterface $cart);

    /**
     * Resets current cart identifier.
     * Basically, it means abandoning current cart.
     */
    public function resetCurrentCartIdentifier();
}
