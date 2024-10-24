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

namespace Sylius\Bundle\ApiBundle\QueryHandler;

use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class GetStatisticsHandler
{
    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        private StatisticsProviderInterface $statisticsProvider,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    public function __invoke(GetStatistics $query): Statistics
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($query->getChannelCode());

        if ($channel === null) {
            throw new ChannelNotFoundException(
                sprintf('Channel with code "%s" does not exist.', $query->getChannelCode()),
            );
        }

        return $this->statisticsProvider->provide($query->getIntervalType(), $query->getDatePeriod(), $channel);
    }
}
