<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Dashboard\SalesSummary;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class SalesDataProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_implements_sales_data_provider_interface(): void
    {
        $this->shouldImplement(SalesDataProviderInterface::class);
    }

    function it_provides_last_year_sales_summary_for_given_channel(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel
    ): void {
        $orderRepository
            ->getLastYearSalesPerMonthForChannel($channel)
            ->willReturn(['01.11' => 1000, '02.11' => 1500])
        ;

        $this->getLastYearSalesSummary($channel)->shouldBeLike(new SalesSummary(['01.11' => 1000, '02.11' => 1500]));
    }
}
