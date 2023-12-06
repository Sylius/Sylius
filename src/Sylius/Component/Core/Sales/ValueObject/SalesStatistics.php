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

namespace Sylius\Component\Core\Sales\ValueObject;

use Webmozart\Assert\Assert;

class SalesStatistics
{
    /** @param SalesInPeriod[] $salesInPeriod */
    public function __construct(
        private array $salesInPeriod,
        private SalesSummary $salesSummary,
        private SalesPeriod $salesPeriod,
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
        return $this->salesSummary->getTotalSales();
    }

    public function getNewCustomersCount(): int
    {
        return $this->salesSummary->getNewCustomersCount();
    }

    public function getNewOrdersCount(): int
    {
        return $this->salesSummary->getNewOrdersCount();
    }

    public function getAverageOrderValue(): int
    {
        return $this->salesSummary->getAverageOrderValue();
    }

    public function getIntervalType(): string
    {
        return $this->salesPeriod->getInterval();
    }
}
