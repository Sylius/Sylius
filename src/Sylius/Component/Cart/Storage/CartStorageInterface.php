<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Storage;

use Sylius\Component\Cart\Model\CartInterface;

/**
 * Interface for service that stores current cart id.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartStorageInterface
{
    /**
     * Returns current cart id, used then to retrieve the cart.
     *
     * @return mixed
     */
    public function getCurrentCartIdentifier();

    /**
     * Sets current cart id and persists it.
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
