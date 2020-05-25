<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller\Dashboard;

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

    /**
     * @var SalesDataProviderInterface
     */
    private $salesDataProvider;

    public function __construct(
        EngineInterface $templatingEngine,
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->statisticsProvider = $statisticsProvider;
        $this->salesDataProvider = $salesDataProvider;
    }

    public function __invoke(ChannelInterface $channel): Response
    {
        return $this->templatingEngine->renderResponse(
            '@SyliusAdmin/Dashboard/Statistics/_template.html.twig', $this->getRawData($channel,
            (new \DateTime('first day of next month last year')),
            (new \DateTime('last day of this month')),
            'month')
        );
    }

    private function getRawData(ChannelInterface $channel, $startDate, $endDate, $interval): array
    {
        /** @var DashboardStatisticsProviderInterface */
        $statistics = $this->statisticsProvider->getStatisticsForChannel($channel);

        $salesSummary = $this->salesDataProvider->getSalesSummary(
            $channel,
            $startDate,
            $endDate,
            Interval::{$interval}()
        );

        return [
            'sales_summary' => [
                'months'=>$salesSummary->getIntervals(),
                'sales' => $salesSummary->getSales()
            ],
            'channel'=>[
              'base_currency_code'=>$channel->getBaseCurrency()->getCode()
            ],
            'statistics'=>[
                'total_sales' => $statistics->getTotalSales(),
                'number_of_new_orders' => $statistics->getNumberOfNewOrders(),
                'number_of_new_customers' => $statistics->getNumberOfNewCustomers(),
                'average_order_value' => $statistics->getAverageOrderValue(),
            ]
        ];
    }

    public function getJsonData(ChannelInterface $channel, $interval): Response
    {
        return new JsonResponse($this->getRawData($channel,
            (new \DateTime('first day of next month last year')),
            (new \DateTime('last day of this month')),
            $interval)
        );
    }
}
