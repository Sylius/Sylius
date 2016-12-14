<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Customer\Statistics;

use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerStatistics
{
    /**
     * @var PerChannelCustomerStatistics[]
     */
    private $perChannelsStatistics;

    /**
     * @param PerChannelCustomerStatistics[] $perChannelStatistics
     */
    public function __construct(array $perChannelStatistics)
    {
        Assert::allIsInstanceOf($perChannelStatistics, PerChannelCustomerStatistics::class);

        $this->perChannelsStatistics = $perChannelStatistics;
    }

    /**
     * @return int
     */
    public function getAllOrdersCount()
    {
        return array_sum(array_map(function (PerChannelCustomerStatistics $statistics) {
            return $statistics->getOrdersCount();
        }, $this->perChannelsStatistics));
    }

    /**
     * @return PerChannelCustomerStatistics[]
     */
    public function getPerChannelsStatistics()
    {
        return $this->perChannelsStatistics;
    }
}
