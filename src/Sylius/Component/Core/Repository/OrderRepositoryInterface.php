<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function createListQueryBuilder(): QueryBuilder;

    /**
     * @param mixed $customerId
     */
    public function createByCustomerIdQueryBuilder($customerId): QueryBuilder;

    /**
     * @param mixed $customerId
     * @param mixed $channelId
     */
    public function createByCustomerAndChannelIdQueryBuilder($customerId, $channelId): QueryBuilder;

    /**
     * @param CustomerInterface $customer
     * @param PromotionCouponInterface $coupon
     *
     * @return int
     */
    public function countByCustomerAndCoupon(CustomerInterface $customer, PromotionCouponInterface $coupon): int;

    /**
     * @param CustomerInterface $customer
     *
     * @return int
     */
    public function countByCustomer(CustomerInterface $customer): int;

    /**
     * @return array|OrderInterface[]
     */
    public function findByCustomer(CustomerInterface $customer): array;

    /**
     * @return array|OrderInterface[]
     */
    public function findForCustomerStatistics(CustomerInterface $customer): array;

    /**
     * @param mixed $id
     */
    public function findOneForPayment($id): ?OrderInterface;

    /**
     * @param string $number
     * @param CustomerInterface $customer
     *
     * @return OrderInterface|null
     */
    public function findOneByNumberAndCustomer(string $number, CustomerInterface $customer): ?OrderInterface;

    /**
     * @param mixed $id
     */
    public function findCartByChannel($id, ChannelInterface $channel): ?OrderInterface;

    /**
     * @param ChannelInterface $channel
     * @param CustomerInterface $customer
     *
     * @return OrderInterface|null
     */
    public function findLatestCartByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer): ?OrderInterface;

    /**
     * @param ChannelInterface $channel
     *
     * @return int
     */
    public function getTotalSalesForChannel(ChannelInterface $channel): int;

    /**
     * @param ChannelInterface $channel
     *
     * @return int
     */
    public function countFulfilledByChannel(ChannelInterface $channel): int;

    /**
     * @param ChannelInterface $channel
     *
     * @return array|OrderInterface[]
     */
    public function findLatestInChannel(int $count, ChannelInterface $channel): array;

    /**
     * @return array|OrderInterface[]
     */
    public function findOrdersUnpaidSince(\DateTimeInterface $terminalDate): array;

    /**
     * @param mixed $id
     */
    public function findCartForSummary($id): ?OrderInterface;

    /**
     * @param mixed $id
     */
    public function findCartForAddressing($id): ?OrderInterface;

    /**
     * @param mixed $id
     */
    public function findCartForSelectingShipping($id): ?OrderInterface;

    /**
     * @param mixed $id
     */
    public function findCartForSelectingPayment($id): ?OrderInterface;
}
