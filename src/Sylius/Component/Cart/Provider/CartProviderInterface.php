<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Provider;

use Sylius\Component\Cart\Model\CartInterface;

/**
 * Used by context to obtain a cart.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartProviderInterface
{
    /**
     * @return null|CartInterface
     */
    public function getCart();
}
