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

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSalesSummary(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $period,
        ChannelInterface $channel,
        string $dateFormat
    ): SalesSummaryInterface {
        $formattedStartDate = $startDate->format('Y/m/d H:i:s');
        $formattedEndDate = $endDate->format('Y/m/d H:i:s');
        $channelId = $channel->getId();

        $query = $this->entityManager->getConnection()->query(
            "SELECT
                DATE_FORMAT(checkout_completed_at, '%m.%y') AS date,
                DATE_FORMAT(checkout_completed_at, '%y.%m') as month,
                DATE_FORMAT(checkout_completed_at, '%y.%m.%d') as day,
                DATE_FORMAT(checkout_completed_at, '%y.%m.%d %H') as hour,
                DATE_FORMAT(checkout_completed_at, '%y') as year,
            SUM(total) as total
            FROM sylius_order
            WHERE (channel_id = $channelId)
            AND (checkout_completed_at BETWEEN '$formattedStartDate' AND '$formattedEndDate')
            AND (payment_state = 'paid')
            GROUP BY '$period';"
        );

        $query->execute();
        $result = $query->fetchAll();

        $data = [];
        foreach ($result as $item) {
            $data[$item['date']] = (int) $item['total'];
        }

        return new SalesSummary($startDate, $endDate, $period, $data, $dateFormat);
    }
}
