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

namespace spec\Sylius\Bundle\ApiBundle\QueryHandler\Admin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Query\Admin\GetSalesStatistics;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Sales\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesStatistics;

final class GetSalesStatisticsHandlerSpec extends ObjectBehavior
{
    function let(SalesStatisticsProviderInterface $salesStatisticsProvider, ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($salesStatisticsProvider, $channelRepository);
    }

    function it_throws_an_exception_if_channel_has_not_been_found(
        SalesStatisticsProviderInterface $salesStatisticsProvider,
        ChannelRepositoryInterface $channelRepository,
        SalesPeriod $salesPeriod,
    ): void {
        $channelRepository->findOneByCode('NON_EXISTING_CHANNEL_CODE')->willReturn(null);

        $salesStatisticsProvider->provide(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during(
                '__invoke',
                [new GetSalesStatistics($salesPeriod->getWrappedObject(), 'NON_EXISTING_CHANNEL_CODE')],
            );
    }

    function it_provides_sales_statistics_for_given_channel(
        SalesStatisticsProviderInterface $salesStatisticsProvider,
        ChannelRepositoryInterface $channelRepository,
        SalesPeriod $salesPeriod,
        ChannelInterface $channel,
        SalesStatistics $salesStatistics,
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);
        $salesStatisticsProvider->provide($salesPeriod, $channel)->willReturn($salesStatistics);

        $this(new GetSalesStatistics($salesPeriod->getWrappedObject(), 'CHANNEL_CODE'))
            ->shouldReturn($salesStatistics);
    }
}
