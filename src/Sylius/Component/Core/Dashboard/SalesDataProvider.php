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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

/**
 * @experimental
 */
final class SalesDataProvider implements SalesDataProviderInterface
{
    /** @var OrderRepositoryInterface|EntityRepository */
    private $orderRepository;

    /** @var IntervalsConverterInterface */
    private $intervalsConverter;

    public function __construct(OrderRepositoryInterface $orderRepository, IntervalsConverterInterface $intervalsConverter)
    {
        $this->orderRepository = $orderRepository;
        $this->intervalsConverter = $intervalsConverter;
    }

    /** @psalm-suppress PossiblyUndefinedMethod */
    function getSalesSummary(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $interval,
        ChannelInterface $channel,
        string $dateFormat
    ): SalesSummaryInterface {
        $ordersTotals = $this->orderRepository->createQueryBuilder('o')
            ->select('HOUR(o.checkoutCompletedAt) AS hour')
            ->addSelect('DAY(o.checkoutCompletedAt) AS day')
            ->addSelect('MONTH(o.checkoutCompletedAt) AS month')
            ->addSelect('YEAR(o.checkoutCompletedAt) AS year')
            ->addSelect('SUM(o.total) AS total')
            ->where('o.checkoutCompletedAt >= :startDate')
            ->andWhere('o.checkoutCompletedAt <= :endDate')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('channel', $channel)
            ->groupBy($interval)
            ->addGroupBy('o.checkoutCompletedAt')
            ->getQuery()
            ->getResult()
        ;

        $data = [];

        foreach ($ordersTotals as $item) {
            $data[$item[$interval]] = 0;
        }
        foreach ($ordersTotals as $item) {
            $data[$item[$interval]] += (int) $item['total'];
        }

        $dateIntervals = $this->intervalsConverter->getIntervals($startDate, $endDate, $interval);

        return new SalesSummary($dateIntervals, $data, $dateFormat);
    }
}
