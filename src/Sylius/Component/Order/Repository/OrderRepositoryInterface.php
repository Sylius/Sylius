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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderRepositoryInterface extends RepositoryInterface
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
     * @param string $orderNumber
     *
     * @return OrderInterface|null
     */
    public function findOneByNumber($orderNumber);

    /**
     * @return OrderInterface|null
     */
    public function findCartById($id);

    /**
     * @param \DateTime $terminalDate
     *
     * @return OrderInterface[]
     */
    public function findCartsNotModifiedSince(\DateTime $terminalDate);

    /**
     * @param \DateTime $terminalDate
     *
     * @return OrderInterface[]
     */
    public function findOrdersUnpaidSince(\DateTime $terminalDate);
}
