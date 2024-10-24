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

namespace Sylius\Component\Core\Statistics\Provider;

use Sylius\Component\Core\Model\ChannelInterface;

interface SalesStatisticsProviderInterface
{
    /** @return array<array{total: int, period: string}> */
    public function provide(string $intervalType, \DatePeriod $datePeriod, ChannelInterface $channel): array;
}
