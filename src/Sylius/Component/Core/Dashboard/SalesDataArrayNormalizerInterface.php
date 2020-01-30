<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

interface SalesDataArrayNormalizerInterface
{
    public function completeNoSalesMonthData(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $salesData
    ): array;
}
