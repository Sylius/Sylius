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
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\OrderPaymentStates;

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var EntityRepository */
    private $orderRepository;

    const YEAR = 'year';
    const MONTH = 'month';
    const DAY = 'day';
    const WEEK = 'week';

    const CHECKOUT_DATE = 'o.checkoutCompletedAt';
    const SELECT_YEAR = "YEAR(".self::CHECKOUT_DATE.")";
    const SELECT_MONTH = "MONTH(".self::CHECKOUT_DATE.")";
    const SELECT_DAY = "DAY(".self::CHECKOUT_DATE.")";
    const SELECT_WEEK = "WEEK(".self::CHECKOUT_DATE.")";

    public function __construct(EntityRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
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
            case self::YEAR:
                $this->buildYearQuery($queryBuilder, $startDate, $endDate);

                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data[self::YEAR];
                };

                break;
            case self::MONTH:
                $this->buildMonthQuery($queryBuilder, $startDate, $endDate);

                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('n.Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data[self::MONTH] . '.' . $data[self::YEAR];
                };

                break;
            case self::WEEK:
                $this->buildWeekQuery($queryBuilder, $startDate, $endDate);

                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return (ltrim($date->format('W'), '0') ?: '0') . ' ' . $date->format('Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data[self::WEEK] . ' ' . $data[self::YEAR];
                };

                break;
            case self::DAY:
                $this->buildDayQuery($queryBuilder, $startDate, $endDate);

                $dateFormatter = static function (\DateTimeInterface $date): string {
                    return $date->format('j.n.Y');
                };
                $resultFormatter = static function (array $data): string {
                    return $data[self::DAY] . '.' . $data[self::MONTH] . '.' . $data[self::YEAR];
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

    private function buildDayQuery(QueryBuilder $queryBuilder, \DateTime$startDate, \DateTime $endDate)
    {
        $queryBuilder
            ->addSelect(self::SELECT_YEAR.' as '.self::YEAR)
            ->addSelect(self::SELECT_MONTH. ' as '.self::MONTH)
            ->addSelect(self::SELECT_DAY.' as '.self::DAY)
            ->groupBy(self::YEAR)
            ->addGroupBy(self::MONTH)
            ->addGroupBy(self::DAY)
            ->andHaving($queryBuilder->expr()->orX(
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' = :endYear AND '.self::SELECT_MONTH.' = :startMonth AND '.self::SELECT_MONTH.' = :endMonth AND '.self::SELECT_DAY.' >= :startDay AND '.self::SELECT_DAY.' <= :endDay',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' = :endYear AND '.self::SELECT_MONTH.' = :startMonth AND '.self::SELECT_MONTH.' != :endMonth AND '.self::SELECT_DAY.' >= :startDay',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' = :endYear AND '.self::SELECT_MONTH.' = :endMonth AND '.self::SELECT_MONTH.' != :startMonth AND '.self::SELECT_DAY.' <= :endDay',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' = :endYear AND '.self::SELECT_MONTH.' > :startMonth AND '.self::SELECT_MONTH.' < :endMonth',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' != :endYear AND '.self::SELECT_MONTH.' = :startMonth AND '.self::SELECT_DAY.' >= :startDay',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' != :endYear AND '.self::SELECT_MONTH.' > :startMonth',
                self::SELECT_YEAR.' = :endYear AND '.self::SELECT_YEAR.' != :startYear AND '.self::SELECT_MONTH.' = :endMonth AND '.self::SELECT_DAY.' <= :endDay',
                self::SELECT_YEAR.' = :endYear AND '.self::SELECT_YEAR.' != :startYear AND '.self::SELECT_MONTH.' < :endMonth',
                self::SELECT_YEAR.' > :startYear AND '.self::SELECT_YEAR.' < :endYear'
            ))
            ->setParameter('startYear', $startDate->format('Y'))
            ->setParameter('startMonth', $startDate->format('n'))
            ->setParameter('startDay', $startDate->format('j'))
            ->setParameter('endYear', $endDate->format('Y'))
            ->setParameter('endMonth', $endDate->format('n'))
            ->setParameter('endDay', $endDate->format('j'))
        ;
    }

    private function buildMonthQuery(QueryBuilder $queryBuilder, \DateTime$startDate, \DateTime $endDate)
    {
        $queryBuilder
            ->addSelect(self::SELECT_YEAR.' as '.self::YEAR)
            ->addSelect(self::SELECT_MONTH. ' as '.self::MONTH)
            ->groupBy(self::YEAR)
            ->addGroupBy(self::MONTH)
            ->andHaving($queryBuilder->expr()->orX(
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' = :endYear AND '.self::SELECT_MONTH.' >= :startMonth AND '.self::SELECT_MONTH.' <= :endMonth',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' != :endYear AND '.self::SELECT_MONTH.' >= :startMonth',
                self::SELECT_YEAR.' = :endYear AND '.self::SELECT_YEAR.' != :startYear AND '.self::SELECT_MONTH.' <= :endMonth',
                self::SELECT_YEAR.' > :startYear AND '.self::SELECT_YEAR.' < :endYear'
            ))
            ->setParameter('startYear', $startDate->format('Y'))
            ->setParameter('startMonth', $startDate->format('n'))
            ->setParameter('endYear', $endDate->format('Y'))
            ->setParameter('endMonth', $endDate->format('n'))
        ;
    }

    private function buildYearQuery(QueryBuilder $queryBuilder, \DateTime$startDate, \DateTime $endDate)
    {
        $queryBuilder
            ->addSelect(self::SELECT_YEAR.' as '.self::YEAR)
            ->groupBy(self::YEAR)
            ->andHaving(self::SELECT_YEAR.' >= :startYear AND '.self::SELECT_YEAR.' <= :endYear')
            ->setParameter('startYear', $startDate->format('Y'))
            ->setParameter('endYear', $endDate->format('Y'))
        ;
    }

    private function buildWeekQuery(QueryBuilder $queryBuilder, \DateTime$startDate, \DateTime $endDate)
    {
        $queryBuilder
            ->addSelect(self::SELECT_YEAR.' as '.self::YEAR)
            ->addSelect(self::SELECT_WEEK. ' as '.self::YEAR)
            ->groupBy(self::YEAR)
            ->addGroupBy(self::YEAR)
            ->andHaving($queryBuilder->expr()->orX(
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' = :endYear AND '.self::SELECT_WEEK.' >= :startWeek AND '.self::SELECT_WEEK.' <= :endWeek',
                self::SELECT_YEAR.' = :startYear AND '.self::SELECT_YEAR.' != :endYear AND '.self::SELECT_WEEK.' >= :startWeek',
                self::SELECT_YEAR.' = :endYear AND '.self::SELECT_YEAR.' != :startYear AND '.self::SELECT_WEEK.' <= :endWeek',
                self::SELECT_YEAR.' > :startYear AND '.self::SELECT_YEAR.' < :endYear'
            ))
            ->setParameter('startYear', $startDate->format('Y'))
            ->setParameter('startWeek', (ltrim($startDate->format('W'), '0') ?: '0'))
            ->setParameter('endYear', $endDate->format('Y'))
            ->setParameter('endWeek', (ltrim($endDate->format('W'), '0') ?: '0'))
        ;
    }
}
