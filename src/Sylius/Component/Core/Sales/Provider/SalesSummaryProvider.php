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

namespace Sylius\Component\Core\Sales\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesSummary;

class SalesSummaryProvider implements SalesSummaryProviderInterface
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(private OrderRepositoryInterface $orderRepository, private CustomerRepositoryInterface $customerRepository)
    {
    }

    public function provide(SalesPeriod $salesPeriod, ChannelInterface $channel): SalesSummary
    {
        return new SalesSummary(
            $this->orderRepository->getTotalPaidSalesForChannelInPeriod($channel, $salesPeriod->getStartDate(), $salesPeriod->getEndDate()),
            $this->orderRepository->countPaidForChannelInPeriod($channel, $salesPeriod->getStartDate(), $salesPeriod->getEndDate()),
            $this->customerRepository->countCustomersInPeriod($salesPeriod->getStartDate(), $salesPeriod->getEndDate()),
        );
    }
}
