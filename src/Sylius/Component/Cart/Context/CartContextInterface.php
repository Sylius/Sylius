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
    /**
     * @return CartInterface
     *
     * @throws CartNotFoundException
     */
    public function getCart();
}
