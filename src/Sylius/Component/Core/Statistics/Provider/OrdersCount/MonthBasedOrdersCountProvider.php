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

namespace Sylius\Component\Core\Statistics\Provider\OrdersCount;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class MonthBasedOrdersCountProvider implements OrdersCountProviderInterface
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    /** @return array<array-key, array{period: \DateTimeInterface, paidOrdersCount: int}> */
    public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array
    {
        /** @param array<array{paid_orders_count: string|int, year: int, month: int}> $amounts */
        $amounts = $this->orderRepository->countGroupedPaidForChannelInPeriod(
            $channel,
            $period->getStartDate(),
            $period->getEndDate(),
            [
                'year' => 'YEAR(o.checkoutCompletedAt) AS year',
                'month' => 'MONTH(o.checkoutCompletedAt) AS month',
            ],
        );

        $result = [];
        foreach ($period as $date) {
            $result[] = [
                'period' => $date,
                'paidOrdersCount' => $this->getAmountForDate($amounts, $date),
            ];
        }

        return $result;
    }

    /** @param array<array{paid_orders_count: string|int, year: int, month: int}> $amounts */
    private function getAmountForDate(array $amounts, \DateTimeInterface $date): int
    {
        $formattedPeriodDate = $date->format('Y-n');

        foreach ($amounts as $entry) {
            $entryDate = $entry['year'] . '-' . $entry['month'];
            if ($formattedPeriodDate === $entryDate) {
                return (int) $entry['paid_orders_count'];
            }
        }

        return 0;
    }
}
