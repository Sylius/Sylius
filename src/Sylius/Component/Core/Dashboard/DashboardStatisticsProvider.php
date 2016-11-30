<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardStatisticsProvider implements DashboardStatisticsProviderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatisticsForChannel(ChannelInterface $channel)
    {
        return new DashboardStatistics(
            $this->orderRepository->getTotalSalesForChannel($channel),
            $this->orderRepository->countByChannel($channel),
            $this->customerRepository->count()
        );
    }
}
