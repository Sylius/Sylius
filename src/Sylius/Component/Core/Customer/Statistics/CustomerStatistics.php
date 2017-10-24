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

namespace Sylius\Component\Core\Customer\Statistics;

use Webmozart\Assert\Assert;

final class CustomerStatistics
{
    /**
     * @var array|PerChannelCustomerStatistics[]
     */
    private $perChannelsStatistics;

    /**
     * @param array|PerChannelCustomerStatistics[] $perChannelStatistics
     */
    public function __construct(array $perChannelStatistics)
    {
        Assert::allIsInstanceOf($perChannelStatistics, PerChannelCustomerStatistics::class);

        $this->perChannelsStatistics = $perChannelStatistics;
    }

    /**
     * @return int
     */
    public function getAllOrdersCount(): int
    {
        return array_sum(array_map(function (PerChannelCustomerStatistics $statistics) {
            return $statistics->getOrdersCount();
        }, $this->perChannelsStatistics));
    }

    /**
     * @return array|PerChannelCustomerStatistics[]
     */
    public function getPerChannelsStatistics(): array
    {
        return $this->perChannelsStatistics;
    }
}
