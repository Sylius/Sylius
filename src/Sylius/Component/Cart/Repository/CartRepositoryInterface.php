<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Repository;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * Order repository interface.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CartRepositoryInterface extends OrderRepositoryInterface
{
    /**
     * Get expired carts
     *
     * @return CartInterface[]
     */
    public function findExpiredCarts();
}
