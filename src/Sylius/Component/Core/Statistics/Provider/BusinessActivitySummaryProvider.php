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

namespace Sylius\Component\Core\Statistics\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\ValueObject\BusinessActivitySummary;

class BusinessActivitySummaryProvider implements BusinessActivitySummaryProviderInterface
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function provide(\DatePeriod $datePeriod, ChannelInterface $channel): BusinessActivitySummary
    {
        return new BusinessActivitySummary(
            $this
                ->orderRepository
                ->getTotalPaidSalesForChannelInPeriod($channel, $datePeriod->getStartDate(), $datePeriod->getEndDate()),
            $this
                ->orderRepository
                ->countPaidForChannelInPeriod($channel, $datePeriod->getStartDate(), $datePeriod->getEndDate()),
            $this
                ->customerRepository
                ->countCustomersInPeriod($datePeriod->getStartDate(), $datePeriod->getEndDate()),
        );
    }
}
