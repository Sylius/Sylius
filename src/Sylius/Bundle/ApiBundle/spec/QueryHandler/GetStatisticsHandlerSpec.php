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

namespace spec\Sylius\Bundle\ApiBundle\QueryHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class GetStatisticsHandlerSpec extends ObjectBehavior
{
    function let(StatisticsProviderInterface $statisticsProvider, ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($statisticsProvider, $channelRepository);
    }

    function it_gets_statistics(
        StatisticsProviderInterface $statisticsProvider,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        GetStatistics $query,
        \DatePeriod $datePeriod,
        Statistics $statistics,
    ): void {
        $query->getChannelCode()->willReturn('CHANNEL_CODE');
        $query->getDatePeriod()->willReturn($datePeriod);
        $query->getIntervalType()->willReturn('day');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);

        $statisticsProvider->provide('day', $datePeriod, $channel)->willReturn($statistics);

        $this->__invoke($query)->shouldBe($statistics);
    }

    function it_throws_channel_not_found_exception_when_channel_is_null(
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $datePeriod = new \DatePeriod(
            new \DateTime('2022-01-01'),
            new \DateInterval('P1D'),
            new \DateTime('2022-12-31'),
        );
        $channelRepository->findOneByCode('NON_EXISTING_CHANNEL_CODE')->willReturn(null);

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during('__invoke', [new GetStatistics('day', $datePeriod, 'NON_EXISTING_CHANNEL_CODE')])
        ;
    }
}
