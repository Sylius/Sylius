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

interface StatisticsDataProviderInterface
{
    /** @return array<array-key, array<array-key, mixed>> */
    public function getRawData(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $interval): array;
}
