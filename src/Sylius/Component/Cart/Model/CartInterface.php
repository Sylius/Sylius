<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Model;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Cart model interface.
 * All driver cart entities or documents should implement this interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartInterface extends OrderInterface
{
    /**
     * Get the identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Gets expiration time.
     *
     * @return \DateTimeInterface
     */
    public function getExpiresAt();

    /**
     * Sets expiration time.
     *
     * @param \DateTimeInterface|null $expiresAt
     */
    public function setExpiresAt(\DateTimeInterface $expiresAt = null);

    /**
     * Bumps the expiration time.
     * Default is +3 hours.
     */
    public function incrementExpiresAt();

    /**
     * Checks whether the cart is expired or not.
     *
     * @return Boolean
     */
    public function isExpired();
}
