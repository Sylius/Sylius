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

/**
 * @experimental
 */
final class SalesSummary implements SalesSummaryInterface
{
    /** @psalm-var array<string, string> */
    private $monthsSalesMap = [];

    public function __construct(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $salesData
    ) {
        $period = new \DatePeriod($startDate, \DateInterval::createFromDateString('1 month'), $endDate);

        /** @var \DateTimeInterface $date */
        foreach ($period as $date) {
            $periodName = $date->format('m.y');
            if (!isset($salesData[$periodName])) {
                $salesData[$periodName] = 0;
            }
        }

        uksort($salesData, function (string $date1, string $date2) {
            return \DateTime::createFromFormat('m.y', $date1) <=> \DateTime::createFromFormat('m.y', $date2);
        });

        foreach ($salesData as $month => $sales) {
            $this->monthsSalesMap[$month] = number_format(abs($sales / 100), 2, '.', '');
        }
    }

    public function getMonths(): array
    {
        return array_keys($this->monthsSalesMap);
    }

    public function getSales(): array
    {
        return array_values($this->monthsSalesMap);
    }
}
