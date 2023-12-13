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
use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\Period;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class GetStatisticsHandlerSpec extends ObjectBehavior
{
    function let(StatisticsProviderInterface $statisticsProvider, ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($statisticsProvider, $channelRepository);
    }

    function it_throws_an_exception_if_channel_has_not_been_found(
        StatisticsProviderInterface $statisticsProvider,
        ChannelRepositoryInterface $channelRepository,
        Period $period,
    ): void {
        $channelRepository->findOneByCode('NON_EXISTING_CHANNEL_CODE')->willReturn(null);

        $statisticsProvider->provide(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during(
                '__invoke',
                [new GetStatistics($period->getWrappedObject(), 'NON_EXISTING_CHANNEL_CODE')],
            );
    }

    function it_provides_sales_statistics_for_given_channel(
        StatisticsProviderInterface $statisticsProvider,
        ChannelRepositoryInterface $channelRepository,
        Period $period,
        ChannelInterface $channel,
        Statistics $statistics,
    ): void {
        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);
        $statisticsProvider->provide($period, $channel)->willReturn($statistics);

        $this(new GetStatistics($period->getWrappedObject(), 'CHANNEL_CODE'))
            ->shouldReturn($statistics);
    }
}
