<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Purger;

/**
 * Interface for the expired carts purger.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface PurgerInterface
{
    /**
     * Purge all expired carts.
     *
     * @return bool
     */
    public function purge();
}
