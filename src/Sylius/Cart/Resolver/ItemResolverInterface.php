<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Cart\Resolver;

use Sylius\Cart\Model\CartItemInterface;

/**
 * Resolver returns cart item that needs to be added based on given data.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ItemResolverInterface
{
    /**
     * Returns item that will be added into the cart.
     *
     * @param CartItemInterface $item Empty and clean item object as first argument
     * @param mixed             $data Mixed data from which item identifier is extracted
     *
     * @return CartItemInterface
     *
     * @throws ItemResolvingException
     */
    public function resolve(CartItemInterface $item, $data);
}
