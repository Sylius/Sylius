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
