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

namespace Sylius\Bundle\AdminBundle\Controller\Dashboard;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class StatisticsController
{
    /** @var EngineInterface */
    private $templatingEngine;

    /** @var DashboardStatisticsProviderInterface */
    private $statisticsProvider;

    /** @var SalesDataProviderInterface */
    private $salesDataProvider;

    /** @var MoneyFormatterInterface */
    private $moneyFormatter;

    public function __construct(
        EngineInterface $templatingEngine,
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        MoneyFormatterInterface $moneyFormatter
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->statisticsProvider = $statisticsProvider;
        $this->salesDataProvider = $salesDataProvider;
        $this->moneyFormatter = $moneyFormatter;
    }

    public function __invoke(ChannelInterface $channel): Response
    {
        return $this->templatingEngine->renderResponse(
            '@SyliusAdmin/Dashboard/Statistics/_template.html.twig', $this->getRawData($channel,
            (new \DateTime('first day of january this year')),
            (new \DateTime('first day of january next year')),
            'month')
        );
    }

    public function getJsonData(ChannelInterface $channel, $interval, $startDate, $endDate): Response
    {
        return new JsonResponse($this->getRawData($channel,
            (new \DateTime($startDate)),
            (new \DateTime($endDate)),
            $interval)
        );
    }

    private function getRawData(ChannelInterface $channel, $startDate, $endDate, $interval): array
    {
        /** @var DashboardStatisticsProviderInterface */
        $statistics = $this->statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate);

        $salesSummary = $this->salesDataProvider->getSalesSummary(
            $channel,
            $startDate,
            $endDate,
            Interval::{$interval}()
        );

        /** @var string */
        $currency_code = $channel->getBaseCurrency()->getCode();

        return [
            'sales_summary' => [
                'months' => $salesSummary->getIntervals(),
                'sales' => $salesSummary->getSales()
            ],
            'channel' => [
              'base_currency_code' => $currency_code
            ],
            'statistics'=>[
                'total_sales' => $this->moneyFormatter->format($statistics->getTotalSales(), $currency_code),
                'number_of_new_orders' => $statistics->getNumberOfNewOrders(),
                'number_of_new_customers' => $statistics->getNumberOfNewCustomers(),
                'average_order_value' => $this->moneyFormatter->format($statistics->getAverageOrderValue(), $currency_code),
            ]
        ];
    }
}
