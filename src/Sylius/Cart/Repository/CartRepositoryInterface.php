<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Cart\Repository;

use Sylius\Cart\Model\CartInterface;
use Sylius\Order\Repository\OrderRepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CartRepositoryInterface extends OrderRepositoryInterface
{
    /**
     * @return CartInterface[]
     */
    public function findExpiredCarts();
}
