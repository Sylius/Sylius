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
        $startDate = (new \DateTime('first day of next month last year'));
        $startDate->setTime(0,0,0);
        $endDate = (new \DateTime('last day of this month'));
        $endDate->setTime(23,59,59);

        $qb = $this->orderRepository->createQueryBuilder('so');
        $qb->select("date_format(so.checkoutCompletedAt, '%m.%y') AS date, SUM(so.total) as total")
            ->where($qb->expr()->eq('so.channel', ':channel'))
            ->andWhere('so.checkoutCompletedAt BETWEEN :startDate AND :endDate')
            ->groupBy('date')
            ->setParameter('channel', $channel)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
        $result = $qb->getQuery()->getScalarResult();

        $data = [];
        foreach ($result as $item) {
            $data[$item['date']] = (int) $item['total'];
        }

        return new SalesSummary(
            $startDate,
            $endDate,
            $data
        );
    }
}
