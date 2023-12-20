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

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Chart\ChartFactoryInterface;
use Sylius\Component\Core\Statistics\Chart\ChartInterface;
use Sylius\Component\Core\Statistics\Provider\SalesTimeSeriesProviderInterface;

final class SalesTimeSeriesProvider implements SalesTimeSeriesProviderInterface
{
    private const SALES = 'sales';

    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ChartFactoryInterface $chartFactory,
    ) {
    }

    public function provide(\DatePeriod $datePeriod, ChannelInterface $channel): ChartInterface
    {
        $ordersTotals = $this->orderRepository->getTotalPaidSalesForChannelInPeriodGroupedByYearAndMonth(
            $channel,
            $datePeriod->start,
            $datePeriod->end,
        );

        return $this->chartFactory->createTimeSeries($datePeriod, [self::SALES => $ordersTotals]);
    }
}
