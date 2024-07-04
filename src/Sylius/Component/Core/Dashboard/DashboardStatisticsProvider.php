<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

trigger_deprecation(
    'sylius/core',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    DashboardStatisticsProvider::class,
);

/**
 * @deprecated since 1.14 and will be removed in Sylius 2.0.
 */
class DashboardStatisticsProvider implements DashboardStatisticsProviderInterface
{
    public function __construct(private OrderRepositoryInterface $orderRepository, private CustomerRepositoryInterface $customerRepository)
    {
    }

    public function getStatisticsForChannel(ChannelInterface $channel): DashboardStatistics
    {
        return new DashboardStatistics(
            $this->orderRepository->getTotalPaidSalesForChannel($channel),
            $this->orderRepository->countPaidByChannel($channel),
            $this->customerRepository->countCustomers(),
            $channel,
        );
    }

    public function getStatisticsForChannelInPeriod(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): DashboardStatistics {
        return new DashboardStatistics(
            $this->orderRepository->getTotalPaidSalesForChannelInPeriod($channel, $startDate, $endDate),
            $this->orderRepository->countPaidForChannelInPeriod($channel, $startDate, $endDate),
            $this->customerRepository->countCustomersInPeriod($startDate, $endDate),
        );
    }
}
