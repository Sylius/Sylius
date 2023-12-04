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

namespace Sylius\Component\Core\Customer\Statistics;

use Sylius\Component\Core\Model\ChannelInterface;

final class PerChannelCustomerStatistics
{
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(private int $ordersCount, private int $ordersValue, private ChannelInterface $channel)
    {
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }

    public function getOrdersValue(): int
    {
        return $this->ordersValue;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getAverageOrderValue(): int
    {
        if (0 === $this->ordersCount) {
            return 0;
        }

        return (int) round($this->ordersValue / $this->ordersCount);
    }
}
