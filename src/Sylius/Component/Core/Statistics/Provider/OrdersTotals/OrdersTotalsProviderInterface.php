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

namespace Sylius\Component\Core\Statistics\Provider\OrdersTotals;

use Sylius\Component\Core\Model\ChannelInterface;

interface OrdersTotalsProviderInterface
{
    /**
     * @return array<array-key, array{period: \DateTimeInterface, total: int}>
     */
    public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array;
}
