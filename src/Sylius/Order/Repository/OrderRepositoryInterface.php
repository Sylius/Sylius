<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Order\Repository;

use Sylius\Order\Model\OrderInterface;
use Sylius\Resource\Repository\RepositoryInterface;
use Sylius\Sequence\Repository\HashSubjectRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderRepositoryInterface extends RepositoryInterface, HashSubjectRepositoryInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @return int
     */
    public function getTotalSales();

    /**
     * @param int $count
     *
     * @return OrderInterface[]
     */
    public function findLatest($count);

    /**
     * @param int|string $number
     *
     * @return bool
     */
    public function isNumberUsed($number);

    /**
     * @param string $orderNumber
     *
     * @return OrderInterface|null
     */
    public function findOneByNumber($orderNumber);
}
