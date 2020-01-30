<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

final class SalesDataArrayNormalizer implements SalesDataArrayNormalizerInterface
{
    public function completeNoSalesMonthData(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $salesData
    ): array {
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

        return $salesData;
    }
}
