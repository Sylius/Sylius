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
use Sylius\Component\Core\Statistics\Mapper\SalesPeriodMapperInterface;
use Sylius\Component\Core\Statistics\Provider\SalesPerPeriodProviderInterface;

final class SalesPerPeriodProvider implements SalesPerPeriodProviderInterface
{
    /** @param EntityRepository<OrderInterface> $orderRepository */
    public function __construct(private EntityRepository $orderRepository, private SalesPeriodMapperInterface $salesPeriodMapper)
    {
    }

    public function provide(Period $period, ChannelInterface $channel): array
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o')
            ->select('SUM(o.total) AS total')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('channel', $channel)
        ;

        switch ($period->getInterval()) {
            case 'year':
                $queryBuilder
                    ->addSelect('YEAR(o.checkoutCompletedAt) as year')
                    ->groupBy('year')
                    ->andWhere('YEAR(o.checkoutCompletedAt) >= :startYear AND YEAR(o.checkoutCompletedAt) <= :endYear')
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
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
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
                    ))
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('startMonth', $period->getStartDate()->format('n'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                    ->setParameter('endMonth', $period->getEndDate()->format('n'))
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
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
                    ))
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('startWeek', (ltrim($period->getStartDate()->format('W'), '0') ?: '0'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                    ->setParameter('endWeek', (ltrim($period->getEndDate()->format('W'), '0') ?: '0'))
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
                        'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
                    ))
                    ->setParameter('startYear', $period->getStartDate()->format('Y'))
                    ->setParameter('startMonth', $period->getStartDate()->format('n'))
                    ->setParameter('startDay', $period->getStartDate()->format('j'))
                    ->setParameter('endYear', $period->getEndDate()->format('Y'))
                    ->setParameter('endMonth', $period->getEndDate()->format('n'))
                    ->setParameter('endDay', $period->getEndDate()->format('j'))
                ;
                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('j.n.Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data['day'] . '.' . $data['month'] . '.' . $data['year'];
                };

                break;
            default:
                throw new \RuntimeException(sprintf('Interval "%s" not supported.', $period->getInterval()));
        }

        $ordersTotals = $queryBuilder->getQuery()->getArrayResult();

        return $this->salesPeriodMapper->map($period, $ordersTotals);
    }
}
