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

namespace Sylius\Component\Core\Statistics\Provider\OrdersTotals;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class YearBasedOrdersTotalProvider implements OrdersTotalsProviderInterface
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array
    {
        /** @var array<array{total: string|int, year: int}> $totals */
        $totals = $this->orderRepository->getGroupedTotalPaidSalesForChannelInPeriod(
            $channel,
            $period->getStartDate(),
            $period->getEndDate(),
            [
                'year' => 'YEAR(o.checkoutCompletedAt) AS year',
            ],
        );

        $result = [];
        foreach ($period as $date) {
            $result[] = [
                'period' => $date,
                'total' => $this->getTotalForDate($totals, $date),
            ];
        }

        return $result;
    }

    /** @param array<array{total: string|int, year: int}> $totals */
    private function getTotalForDate(array $totals, \DateTimeInterface $date): int
    {
        $formattedPeriodDate = (int) $date->format('Y');

        foreach ($totals as $entry) {
            if ($formattedPeriodDate === $entry['year']) {
                return (int) $entry['total'];
            }
        }

        return 0;
    }
}
