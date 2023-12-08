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

namespace Sylius\Component\Core\Statistics\ValueObject;

use Sylius\Component\Core\DateTime\Period;
use Webmozart\Assert\Assert;

class Statistics
{
    /** @param SalesInPeriod[] $salesInPeriod */
    public function __construct(
        private array $salesInPeriod,
        private BusinessActivitySummary $businessActivitySummary,
        private Period $period,
    ) {
        Assert::allIsInstanceOf($salesInPeriod, SalesInPeriod::class);
    }

    /** @return SalesInPeriod[] */
    public function getSalesPerPeriod(): array
    {
        return $this->salesInPeriod;
    }

    public function getTotalSales(): int
    {
        return $this->businessActivitySummary->getTotalSales();
    }

    public function getNewCustomersCount(): int
    {
        return $this->businessActivitySummary->getNewCustomersCount();
    }

    public function getNewOrdersCount(): int
    {
        return $this->businessActivitySummary->getNewOrdersCount();
    }

    public function getAverageOrderValue(): int
    {
        return $this->businessActivitySummary->getAverageOrderValue();
    }

    public function getIntervalType(): string
    {
        return $this->period->getInterval();
    }
}
