<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Repository;

use Sylius\Bundle\OrderBundle\Repository\OrderRepositoryInterface;

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
     * @return array
     */
    public function findExpiredCarts();
}
