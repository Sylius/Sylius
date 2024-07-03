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

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;

trigger_deprecation(
    'sylius/admin-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
    StatisticsDataProviderInterface::class,
    StatisticsProviderInterface::class,
);

/**
 * @deprecated since 1.14 and will be removed in Sylius 2.0. Use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface instead
 */
interface StatisticsDataProviderInterface
{
    /** @return array<array-key, array<array-key, mixed>> */
    public function getRawData(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $interval): array;
}
