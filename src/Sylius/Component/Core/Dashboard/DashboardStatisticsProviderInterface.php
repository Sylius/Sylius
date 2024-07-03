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

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;

trigger_deprecation(
    'sylius/core',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    DashboardStatisticsProviderInterface::class,
);

interface DashboardStatisticsProviderInterface
{
    public function getStatisticsForChannel(ChannelInterface $channel): DashboardStatistics;

    public function getStatisticsForChannelInPeriod(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): DashboardStatistics;
}
