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

namespace Sylius\Component\Core\ValueObject;

use Sylius\Component\Core\Dashboard\Interval;
use Webmozart\Assert\Assert;

class SalesStatistics
{
    /** @param SalesInPeriod[] $salesInPeriod */
    public function __construct(
        private array $salesInPeriod,
        private Interval $intervalType,
        private int $newCustomersCount,
        private int $newOrdersCount,
        private int $totalSales,
        private int $averageOrderValue,
    ) {
        Assert::allIsInstanceOf($salesInPeriod, SalesInPeriod::class);
    }

    /** @return SalesInPeriod[] */
    public function getSalesInPeriod(): array
    {
        return $this->salesInPeriod;
    }

    public function getTotalSales(): int
    {
        return $this->totalSales;
    }

    public function getNewCustomersCount(): int
    {
        return $this->newCustomersCount;
    }

    public function getNewOrdersCount(): int
    {
        return $this->newOrdersCount;
    }

    public function getAverageOrderValue(): int
    {
        return $this->averageOrderValue;
    }

    public function getIntervalType(): string
    {
        return $this->intervalType->asString();
    }
}
