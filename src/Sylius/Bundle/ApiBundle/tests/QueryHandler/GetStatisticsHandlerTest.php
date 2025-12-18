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

namespace Tests\Sylius\Bundle\ApiBundle\QueryHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Sylius\Bundle\ApiBundle\QueryHandler\GetStatisticsHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class GetStatisticsHandlerTest extends TestCase
{
    private MockObject&StatisticsProviderInterface $statisticsProvider;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private GetStatisticsHandler $getStatisticsHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->statisticsProvider = $this->createMock(StatisticsProviderInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->getStatisticsHandler = new GetStatisticsHandler($this->statisticsProvider, $this->channelRepository);
    }

    public function testGetsStatistics(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var GetStatistics|MockObject $queryMock */
        $queryMock = $this->createMock(GetStatistics::class);
        /** @var DatePeriod|MockObject $datePeriodMock */
        $datePeriodMock = $this->createMock(\DatePeriod::class);
        /** @var Statistics|MockObject $statisticsMock */
        $statisticsMock = $this->createMock(Statistics::class);

        $queryMock->expects(self::once())->method('getChannelCode')->willReturn('CHANNEL_CODE');

        $queryMock->expects(self::once())->method('getDatePeriod')->willReturn($datePeriodMock);

        $queryMock->expects(self::once())->method('getIntervalType')->willReturn('day');

        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('CHANNEL_CODE')
            ->willReturn($channelMock);

        $this->statisticsProvider->expects(self::once())
            ->method('provide')
            ->with('day', $datePeriodMock, $channelMock)
            ->willReturn($statisticsMock);

        self::assertSame($statisticsMock, $this->getStatisticsHandler->__invoke($queryMock));
    }

    public function testThrowsChannelNotFoundExceptionWhenChannelIsNull(): void
    {
        $datePeriod = new \DatePeriod(
            new \DateTime('2022-01-01'),
            new \DateInterval('P1D'),
            new \DateTime('2022-12-31'),
        );

        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('NON_EXISTING_CHANNEL_CODE')
            ->willReturn(null);

        self::expectException(ChannelNotFoundException::class);

        $this->getStatisticsHandler->__invoke(
            new GetStatistics(
                'day',
                $datePeriod,
                'NON_EXISTING_CHANNEL_CODE',
            ),
        );
    }
}
