<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

use Sylius\Component\Core\Model\ChannelInterface;

interface DashboardStatisticsProviderInterface
{
    // Use typehint instead of PHPDoc once PHP <= 7.3 support is dropped.
    /**
     * @return DashboardStatisticsInterface
     */
    public function getStatisticsForChannel(ChannelInterface $channel);

    // Use typehint instead of PHPDoc once PHP <= 7.3 support is dropped.
    /**
     * @return DashboardStatisticsInterface
     */
    public function getStatisticsForChannelInPeriod(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    );
}
