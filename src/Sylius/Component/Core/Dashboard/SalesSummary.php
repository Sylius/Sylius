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
    private $intervalsSalesMap = [];

    public function __construct(
        \DatePeriod $datePeriod,
        array $salesData,
        string $dateFormat
    ) {
        /** @var \DateTimeInterface $date */
        foreach ($datePeriod as $date) {
            $periodName = $date->format($dateFormat);
            if (!isset($salesData[$periodName])) {
                $salesData[$periodName] = 0;
            }
        }

        uksort($salesData, function (string $date1, string $date2) use ($dateFormat) {
            return \DateTime::createFromFormat($dateFormat, $date1) <=> \DateTime::createFromFormat($dateFormat, $date2);
        });

        foreach ($salesData as $interval => $sales) {
            $this->intervalsSalesMap[$interval] = number_format(abs($sales / 100), 2, '.', '');
        }
    }

    public function getIntervals(): array
    {
        return array_keys($this->intervalsSalesMap);
    }

    public function getSales(): array
    {
        return array_values($this->intervalsSalesMap);
    }
}
