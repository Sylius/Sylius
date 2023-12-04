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

namespace Sylius\Bundle\ApiBundle\QueryHandler\Admin;

use Sylius\Bundle\ApiBundle\Query\Admin\GetSalesStatistics;
use Sylius\Bundle\CoreBundle\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\ValueObject\SalesStatistics;

/** @experimental */
final class GetSalesStatisticsHandler
{
    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        private SalesStatisticsProviderInterface $salesStatisticsProvider,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    public function __invoke(GetSalesStatistics $query): SalesStatistics
    {
        $startDate = new \DateTimeImmutable('first day of january this year');
        $endDate = new \DateTimeImmutable('first day of january next year');

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($query->getChannelCode());

        if ($channel === null) {
            throw new \InvalidArgumentException(sprintf('Channel with code "%s" does not exist.', $query->getChannelCode()));
        }

        return $this->salesStatisticsProvider->provide(
            $channel,
            $startDate,
            $endDate,
            Interval::month(),
        );
    }
}
