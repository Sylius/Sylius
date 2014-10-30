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
use Sylius\Component\Sequence\Repository\HashSubjectRepositoryInterface;

/**
 * Order repository interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderRepositoryInterface extends RepositoryInterface, HashSubjectRepositoryInterface
{
    /**
     * Gets recently completed orders.
     *
     * @param integer $amount
     *
     * @return OrderInterface[]
     */
    public function findRecentOrders($amount = 10);

    /**
     * Was that order number already used?
     *
     * @param int|string $number
     *
     * @return bool
     */
    public function isNumberUsed($number);
}
