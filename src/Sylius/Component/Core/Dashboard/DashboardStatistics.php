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

class DashboardStatistics
{
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private int $totalSales,
        private int $numberOfNewOrders,
        private int $numberOfNewCustomers,
        private ?ChannelInterface $channel = null,
    ) {
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function getTotalSales(): int
    {
        return $this->totalSales;
    }

    public function getNumberOfNewOrders(): int
    {
        return $this->numberOfNewOrders;
    }

    public function getNumberOfNewCustomers(): int
    {
        return $this->numberOfNewCustomers;
    }

    public function getAverageOrderValue(): int
    {
        if (0 === $this->numberOfNewOrders) {
            return 0;
        }

        return (int) round($this->totalSales / $this->numberOfNewOrders);
    }
}
