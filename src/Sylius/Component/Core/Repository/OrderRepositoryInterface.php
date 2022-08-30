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
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

interface OrderRepositoryInterface extends BaseOrderRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function createSearchListQueryBuilder(): QueryBuilder;

    public function createByCustomerIdQueryBuilder($customerId): QueryBuilder;

    public function createByCustomerAndChannelIdQueryBuilder($customerId, $channelId): QueryBuilder;

    public function countByCustomerAndCoupon(CustomerInterface $customer, PromotionCouponInterface $coupon): int;

    public function countByCustomer(CustomerInterface $customer): int;

    /**
     * @return array|OrderInterface[]
     */
    public function findByCustomer(CustomerInterface $customer): array;

    /**
     * @return array|OrderInterface[]
     */
    public function findForCustomerStatistics(CustomerInterface $customer): array;

    public function findOneForPayment($id): ?OrderInterface;

    public function findOneByNumberAndCustomer(string $number, CustomerInterface $customer): ?OrderInterface;

    public function findCartByChannel($id, ChannelInterface $channel): ?OrderInterface;

    public function findLatestCartByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer): ?OrderInterface;

    public function findLatestNotEmptyCartByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer): ?OrderInterface;

    public function getTotalSalesForChannel(ChannelInterface $channel): int;

    public function getTotalPaidSalesForChannel(ChannelInterface $channel): int;

    public function getTotalPaidSalesForChannelInPeriod(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate): int;

    public function countFulfilledByChannel(ChannelInterface $channel): int;

    public function countPaidByChannel(ChannelInterface $channel): int;

    public function countPaidForChannelInPeriod(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate): int;

    /**
     * @return array|OrderInterface[]
     */
    public function findLatestInChannel(int $count, ChannelInterface $channel): array;

    /**
     * @return array|OrderInterface[]
     */
    public function findOrdersUnpaidSince(\DateTimeInterface $terminalDate): array;

    public function findCartForSummary($id): ?OrderInterface;

    public function findCartForAddressing($id): ?OrderInterface;

    public function findCartForSelectingShipping($id): ?OrderInterface;

    public function findCartForSelectingPayment($id): ?OrderInterface;

    public function findCartByTokenValue(string $tokenValue): ?BaseOrderInterface;

    public function findCartByTokenValueAndChannel(string $tokenValue, ChannelInterface $channel): ?BaseOrderInterface;
}
