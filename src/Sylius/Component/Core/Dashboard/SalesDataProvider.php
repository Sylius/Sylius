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

namespace Sylius\Component\Core\Dashboard;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepositoryInterface $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    public function getLastYearSalesSummary(ChannelInterface $channel): SalesSummaryInterface
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

        return new SalesSummary(
            (new \DateTime('first day of next month last year')),
            (new \DateTime('last day of this month')),
            $data
        );
    }
}
