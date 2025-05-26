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

final class YearBasedOrdersCountProvider implements OrdersCountProviderInterface
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    /** @return array<array-key, array{period: \DateTimeInterface, paidOrdersCount: int}> */
    public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array
    {
        /** @var array<array{paid_orders_count: string|int, year: int}> $amounts */
        $amounts = $this->orderRepository->countGroupedPaidForChannelInPeriod(
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
                'paidOrdersCount' => $this->getAmountForDate($amounts, $date),
            ];
        }

        return $result;
    }

    /** @param array<array{paid_orders_count: string|int, year: int}> $amounts */
    private function getAmountForDate(array $amounts, \DateTimeInterface $date): int
    {
        $formattedPeriodDate = $date->format('Y');

        foreach ($amounts as $entry) {
            if ($formattedPeriodDate === (string) $entry['year']) {
                return (int) $entry['paid_orders_count'];
            }
        }

        return 0;
    }
}
