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

namespace Sylius\Component\Core\Sales\Mapper;

use Sylius\Component\Core\Sales\ValueObject\SalesInPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;

class SalesPeriodMapper implements SalesPeriodMapperInterface
{
    public function map(SalesPeriod $salesPeriod, array $ordersTotals): array
    {
        $salesData = [];

        $period = new \DatePeriod(
            $salesPeriod->getStartDate(),
            \DateInterval::createFromDateString(sprintf('1 %s', $salesPeriod->getInterval())),
            $salesPeriod->getEndDate(),
        );

        foreach ($period as $date) {
            $salesData[] = new SalesInPeriod($this->findTotalSalesForDate($date, $ordersTotals), $date);
        }

        return $salesData;
    }

    /** @param array<array-key, array<array-key, mixed>> $ordersTotals */
    private function findTotalSalesForDate(\DateTimeInterface $date, array $ordersTotals): int
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');

        foreach ($ordersTotals as $orderTotal) {
            if ((int) $orderTotal['year'] === $year && (int) $orderTotal['month'] === $month) {
                return (int) $orderTotal['total'];
            }
        }

        return 0;
    }
}
