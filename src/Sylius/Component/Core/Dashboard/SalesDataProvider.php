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

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\OrderPaymentStates;

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    public function __construct(private EntityRepository $orderRepository)
    {
    }

    public function getSalesSummary(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        Interval $interval
    ): SalesSummaryInterface {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o')
            ->select('SUM(o.total) AS total')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('channel', $channel)
        ;

        switch ($interval->asString()) {
            case 'year':
                $queryBuilder
                    ->addSelect('YEAR(o.checkoutCompletedAt) as year')
                    ->groupBy('year')
                    ->andWhere('YEAR(o.checkoutCompletedAt) >= :startYear AND YEAR(o.checkoutCompletedAt) <= :endYear')
                    ->setParameter('startYear', $startDate->format('Y'))
                    ->setParameter('endYear', $endDate->format('Y'))
                ;
                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('Y');
                };
                $resultFormatter = static function (array $data): string {
                    return (string) $data['year'];
                };

                break;
            case 'month':
                $queryBuilder
                    ->addSelect('YEAR(o.checkoutCompletedAt) as year')
                    ->addSelect('MONTH(o.checkoutCompletedAt) as month')
                    ->groupBy('year')
                    ->addGroupBy('month')
                    ->andWhere($queryBuilder->expr()->orX(
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) >= :startMonth AND MONTH(o.checkoutCompletedAt) <= :endMonth',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND MONTH(o.checkoutCompletedAt) >= :startMonth',
                        'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND MONTH(o.checkoutCompletedAt) <= :endMonth',
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear'
                    ))
                    ->setParameter('startYear', $startDate->format('Y'))
                    ->setParameter('startMonth', $startDate->format('n'))
                    ->setParameter('endYear', $endDate->format('Y'))
                    ->setParameter('endMonth', $endDate->format('n'))
                ;
                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('n.Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data['month'] . '.' . $data['year'];
                };

                break;
            case 'week':
                $queryBuilder
                    ->addSelect('YEAR(o.checkoutCompletedAt) as year')
                    ->addSelect('WEEK(o.checkoutCompletedAt) as week')
                    ->groupBy('year')
                    ->addGroupBy('week')
                    ->andWhere($queryBuilder->expr()->orX(
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND WEEK(o.checkoutCompletedAt) >= :startWeek AND WEEK(o.checkoutCompletedAt) <= :endWeek',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND WEEK(o.checkoutCompletedAt) >= :startWeek',
                        'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND WEEK(o.checkoutCompletedAt) <= :endWeek',
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear'
                    ))
                    ->setParameter('startYear', $startDate->format('Y'))
                    ->setParameter('startWeek', (ltrim($startDate->format('W'), '0') ?: '0'))
                    ->setParameter('endYear', $endDate->format('Y'))
                    ->setParameter('endWeek', (ltrim($endDate->format('W'), '0') ?: '0'))
                ;
                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return (ltrim($date->format('W'), '0') ?: '0') . ' ' . $date->format('Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data['week'] . ' ' . $data['year'];
                };

                break;
            case 'day':
                $queryBuilder
                    ->addSelect('YEAR(o.checkoutCompletedAt) as year')
                    ->addSelect('MONTH(o.checkoutCompletedAt) as month')
                    ->addSelect('DAY(o.checkoutCompletedAt) as day')
                    ->groupBy('year')
                    ->addGroupBy('month')
                    ->addGroupBy('day')
                    ->andWhere($queryBuilder->expr()->orX(
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) = :startMonth AND MONTH(o.checkoutCompletedAt) = :endMonth AND DAY(o.checkoutCompletedAt) >= :startDay AND DAY(o.checkoutCompletedAt) <= :endDay',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) = :startMonth AND MONTH(o.checkoutCompletedAt) != :endMonth AND DAY(o.checkoutCompletedAt) >= :startDay',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) = :endMonth AND MONTH(o.checkoutCompletedAt) != :startMonth AND DAY(o.checkoutCompletedAt) <= :endDay',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) > :startMonth AND MONTH(o.checkoutCompletedAt) < :endMonth',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND MONTH(o.checkoutCompletedAt) = :startMonth AND DAY(o.checkoutCompletedAt) >= :startDay',
                        'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND MONTH(o.checkoutCompletedAt) > :startMonth',
                        'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND MONTH(o.checkoutCompletedAt) = :endMonth AND DAY(o.checkoutCompletedAt) <= :endDay',
                        'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND MONTH(o.checkoutCompletedAt) < :endMonth',
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear'
                    ))
                    ->setParameter('startYear', $startDate->format('Y'))
                    ->setParameter('startMonth', $startDate->format('n'))
                    ->setParameter('startDay', $startDate->format('j'))
                    ->setParameter('endYear', $endDate->format('Y'))
                    ->setParameter('endMonth', $endDate->format('n'))
                    ->setParameter('endDay', $endDate->format('j'))
                ;
                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('j.n.Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data['day'] . '.' . $data['month'] . '.' . $data['year'];
                };

                break;
            default:
                throw new \RuntimeException(sprintf('Interval "%s" not supported.', $interval->asString()));
        }

        $ordersTotals = $queryBuilder->getQuery()->getArrayResult();

        $salesData = [];

        $period = new \DatePeriod($startDate, \DateInterval::createFromDateString(sprintf('1 %s', $interval->asString())), $endDate);
        foreach ($period as $date) {
            $salesData[$dateFormatter($date)] = 0;
        }

        foreach ($ordersTotals as $item) {
            $salesData[$resultFormatter($item)] = (int) $item['total'];
        }

        $salesData = array_map(
            static function (int $total): string {
                return number_format(abs($total / 100), 2, '.', '');
            },
            $salesData
        );

        return new SalesSummary($salesData);
    }
}
