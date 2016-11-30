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

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

interface OrderRepositoryInterface extends BaseOrderRepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @param int $customerId
     *
     * @return QueryBuilder
     */
    public function createByCustomerIdQueryBuilder($customerId);

    /**
     * @param CustomerInterface $customer
     * @param PromotionCouponInterface $coupon
     *
     * @return int
     */
    public function countByCustomerAndCoupon(CustomerInterface $customer, PromotionCouponInterface $coupon);

    /**
     * @param CustomerInterface $customer
     *
     * @return int
     */
    public function countByCustomer(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     *
     * @return OrderInterface[]
     */
    public function findByCustomer(CustomerInterface $customer);
    
    /**
     * @param int $id
     *
     * @return OrderInterface|null
     */
    public function findOneForPayment($id);

    /**
     * @param string $number
     * @param CustomerInterface $customer
     *
     * @return OrderInterface|null
     */
    public function findOneByNumberAndCustomer($number, CustomerInterface $customer);

    /**
     * @param string $id
     * @param ChannelInterface $channel
     *
     * @return OrderInterface|null
     */
    public function findCartByChannel($id, ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     * @param CustomerInterface $customer
     *
     * @return OrderInterface|null
     */
    public function findLatestCartByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer);

    /**
     * @param ChannelInterface $channel
     *
     * @return int
     */
    public function getTotalSalesForChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     *
     * @return int
     */
    public function countByChannel(ChannelInterface $channel);

    /**
     * @param int $count
     * @param ChannelInterface $channel
     *
     * @return OrderInterface[]
     */
    public function findLatestInChannel($count, ChannelInterface $channel);
}
