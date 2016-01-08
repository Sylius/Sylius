<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

interface OrderRepositoryInterface extends BaseOrderRepositoryInterface
{
    /**
     * Gets expired orders.
     *
     * @param \DateTime $expiresAt
     * @param string    $state
     *
     * @return OrderInterface[]
     */
    public function findExpired(\DateTime $expiresAt, $state = OrderInterface::STATE_PENDING);

    /**
     * Gets the number of orders placed by the customer
     * for a particular coupon.
     *
     * @param CustomerInterface $customer
     * @param CouponInterface   $coupon
     *
     * @return int
     */
    public function countByCustomerAndCoupon(CustomerInterface $customer, CouponInterface $coupon);

    /**
     * Gets the number of orders placed by the customer
     * with particular state.
     *
     * @param CustomerInterface $customer
     * @param string            $state
     *
     * @return int
     */
    public function countByCustomerAndPaymentState(CustomerInterface $customer, $state);

    /**
     * Gets revenue group by date
     * between particular dates
     *
     * @param array $configuration
     *
     * @return array
     */
    public function revenueBetweenDatesGroupByDate(array $configuration = []);

    /**
     * Gets number of orders group by date
     * between particular dates
     * 
     * @param array $configuration
     *
     * @return array
     */
    public function ordersBetweenDatesGroupByDate(array $configuration = []);
}
