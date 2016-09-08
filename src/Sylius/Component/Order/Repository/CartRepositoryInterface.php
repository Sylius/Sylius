<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Repository;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartRepositoryInterface extends RepositoryInterface
{
    /**
     * @return null|OrderInterface
     */
    public function findCartById($id);

    /**
     * @return OrderInterface[]
     */
    public function findExpiredCarts();
}
