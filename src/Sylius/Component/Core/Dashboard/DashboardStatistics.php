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
    /** @var int */
    private $totalSales;

    /** @var int */
    private $numberOfNewOrders;

    /** @var int */
    private $numberOfNewCustomers;

    /** @var ChannelInterface */
    private $channel;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(int $totalSales, int $numberOfNewOrders, int $numberOfNewCustomers, ?ChannelInterface $channel = null)
    {
        $this->totalSales = $totalSales;
        $this->numberOfNewOrders = $numberOfNewOrders;
        $this->numberOfNewCustomers = $numberOfNewCustomers;
        $this->channel = $channel;
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
