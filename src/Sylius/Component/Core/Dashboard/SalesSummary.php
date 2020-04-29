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
        string $interval,
        array $salesData,
        string $dateFormat
    ) {
        $interval = new \DatePeriod($startDate, \DateInterval::createFromDateString(sprintf('1 %s ', $interval)), $endDate);

        /** @var \DateTimeInterface $date */
        foreach ($interval as $date) {
            $periodName = $date->format($dateFormat);
            if (!isset($salesData[$periodName])) {
                $salesData[$periodName] = 0;
            }
        }

        uksort($salesData, function (string $date1, string $date2) {
            return \DateTime::createFromFormat('m.y', $date1) <=> \DateTime::createFromFormat('m.y', $date2);
        });

        foreach ($salesData as $interval => $sales) {
            $this->monthsSalesMap[$interval] = number_format(abs($sales / 100), 2, '.', '');
        }
    }

    public function getIntervals(): array
    {
        return array_keys($this->monthsSalesMap);
    }

    public function getSales(): array
    {
        return array_values($this->monthsSalesMap);
    }
}
