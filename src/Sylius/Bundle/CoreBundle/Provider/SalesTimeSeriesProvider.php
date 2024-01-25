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
use Sylius\Component\Core\Statistics\Chart\ChartFactoryInterface;
use Sylius\Component\Core\Statistics\Chart\ChartInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Provider\SalesTimeSeriesProviderInterface;

final class SalesTimeSeriesProvider implements SalesTimeSeriesProviderInterface
{
    private const SALES = 'sales';

    public function __construct(
        private OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        private ChartFactoryInterface $chartFactory,
    ) {
    }

    public function provide(string $intervalType, \DatePeriod $datePeriod, ChannelInterface $channel): ChartInterface
    {
        $provider = $this->ordersTotalsProviderRegistry->getByType($intervalType);
        $ordersTotals = $provider->provideForPeriodInChannel($datePeriod, $channel);

        return $this->chartFactory->createTimeSeries($intervalType, self::SALES, $ordersTotals);
    }
}
