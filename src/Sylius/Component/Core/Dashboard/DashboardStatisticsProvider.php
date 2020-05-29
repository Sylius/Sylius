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

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class DashboardStatisticsProvider implements DashboardStatisticsProviderInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    public function getStatisticsForChannel(ChannelInterface $channel): DashboardStatistics
    {
        return new DashboardStatistics(
            $this->orderRepository->getTotalPaidSalesForChannel($channel),
            $this->orderRepository->countPaidByChannel($channel),
            $this->customerRepository->countCustomers(),
            $channel
        );
    }

    public function getStatisticsForChannelInPeriod(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): DashboardStatistics {
        return new DashboardStatistics(
            $this->orderRepository->getTotalPaidSalesForChannelInPeriod($channel, $startDate, $endDate),
            $this->orderRepository->countPaidForChannelInPeriod($channel, $startDate, $endDate),
            $this->customerRepository->countCustomersInPeriod($startDate, $endDate)
        );
    }
}
