<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SalesDataArrayNormalizerInterface */
    private $salesDataArrayNormalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SalesDataArrayNormalizerInterface $salesDataArrayNormalizer
    ) {
        $this->entityManager = $entityManager;
        $this->salesDataArrayNormalizer = $salesDataArrayNormalizer;
    }

    public function getLastYearSalesSummary(ChannelInterface $channel): SalesSummary
    {
        $startDate = (new \DateTime('first day of next month last year'))->format('Y/m/d');
        $endDate = (new \DateTime('last day of this month'))->format('Y/m/d');
        $channelId = $channel->getId();

        $query = $this->entityManager->getConnection()->query(
            "SELECT
                DATE_FORMAT(checkout_completed_at, '%m.%y') AS \"date\",
                SUM(total) as \"total\"
            FROM sylius_order
            WHERE (channel_id = $channelId)
            AND (checkout_completed_at BETWEEN '$startDate' AND '$endDate')
            AND (payment_state = 'paid')
            GROUP BY date;"
        );

        $query->execute();
        $result = $query->fetchAll();

        $data = [];
        foreach ($result as $item) {
            $data[$item['date']] = (int) $item['total'];
        }

        return new SalesSummary($this->salesDataArrayNormalizer->completeNoSalesMonthData(
            (new \DateTime('first day of next month last year')),
            (new \DateTime('last day of this month')),
            $data
        ));
    }
}
