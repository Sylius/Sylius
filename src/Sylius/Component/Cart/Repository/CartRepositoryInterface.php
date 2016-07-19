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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartRepositoryInterface extends RepositoryInterface
{
    /**
     * @return null|CartInterface
     */
    public function findCartById($id);

    /**
     * @return CartInterface[]
     */
    public function findExpiredCarts();
}
