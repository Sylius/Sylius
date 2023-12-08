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

namespace spec\Sylius\Component\Core\Statistics\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\Period;
use Sylius\Component\Core\Statistics\ValueObject\SalesSummary;

final class SalesSummaryProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, CustomerRepositoryInterface $customerRepository): void
    {
        $this->beConstructedWith($orderRepository, $customerRepository);
    }

    function it_implements_sales_summary_provider_interface(): void
    {
        $this->shouldImplement(BusinessActivitySummaryProviderInterface::class);
    }

    function it_provides_order_and_customer_summary_statistics_by_given_period_and_channel(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelInterface $channel,
        Period $period,
    ): void {
        $expectedSummary = new SalesSummary(totalSales: 450, newOrdersCount: 6, newCustomersCount: 2);

        $startDate = new \DateTimeImmutable('first day of january this year 00:00:00');
        $endDate = new \DateTimeImmutable('last day of december this year 23:59:59');

        $period->getStartDate()->willReturn($startDate);
        $period->getEndDate()->willReturn($endDate);
        $period->getInterval()->willReturn('year');

        $orderRepository->getTotalPaidSalesForChannelInPeriod($channel, $startDate, $endDate)->willReturn(450);
        $orderRepository->countPaidForChannelInPeriod($channel, $startDate, $endDate)->willReturn(6);
        $customerRepository->countCustomersInPeriod($startDate, $endDate)->willReturn(2);

        $this->provide($period, $channel)->shouldBeLike($expectedSummary);
    }
}
