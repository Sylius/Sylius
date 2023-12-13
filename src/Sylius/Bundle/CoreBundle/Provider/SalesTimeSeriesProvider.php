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

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\DateTime\Period;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Statistics\Chart\ChartFactoryInterface;
use Sylius\Component\Core\Statistics\Chart\ChartInterface;
use Sylius\Component\Core\Statistics\Provider\SalesTimeSeriesProviderInterface;

final class SalesTimeSeriesProvider implements SalesTimeSeriesProviderInterface
{
    private const SALES = 'sales';

    /** @param EntityRepository<OrderInterface> $orderRepository */
    public function __construct(private EntityRepository $orderRepository, private ChartFactoryInterface $chartFactory)
    {
    }

    public function provide(Period $period, ChannelInterface $channel): ChartInterface
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o')
            ->select('SUM(o.total) AS total')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('channel', $channel)
        ;

        switch ($period->getIntervalType()) {
            case 'year':
                $queryBuilder
                    ->addSelect('YEAR(o.checkoutCompletedAt) as year')
                    ->groupBy('year')
                    ->andWhere('YEAR(o.checkoutCompletedAt) >= :startYear AND YEAR(o.checkoutCompletedAt) <= :endYear')
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                ;

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
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
                    ))
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('startMonth', $period->getStartDate()->format('n'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                    ->setParameter('endMonth', $period->getEndDate()->format('n'))
                ;

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
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
                    ))
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('startWeek', (ltrim($period->getStartDate()->format('W'), '0') ?: '0'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                    ->setParameter('endWeek', (ltrim($period->getEndDate()->format('W'), '0') ?: '0'))
                ;

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
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
                    ))
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('startMonth', $period->getStartDate()->format('n'))
                    ->setParameter('startDay', $period->getStartDate()->format('j'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                    ->setParameter('endMonth', $period->getEndDate()->format('n'))
                    ->setParameter('endDay', $period->getEndDate()->format('j'))
                ;

                break;
            default:
                throw new \RuntimeException(sprintf('Interval "%s" not supported.', $period->getIntervalType()));
        }

        $ordersTotals = $queryBuilder->getQuery()->getArrayResult();

        return $this->chartFactory->createTimeSeries($period, [self::SALES => $ordersTotals]);
    }
}
