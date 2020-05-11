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

namespace Sylius\Component\Core\Provider;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class MySQLSalesSummaryProvider implements SalesSummaryProviderInterface
{
    /** @var OrderRepositoryInterface|EntityRepository */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getSalesSummary(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        /** @psalm-suppress PossiblyUndefinedMethod */
        $queryBuilder = $this->orderRepository->createQueryBuilder('o')
            ->select("DATE_FORMAT(o.checkoutCompletedAt, '%m.%y') AS date")
            ->addSelect("SUM(o.total) as total")
            ->where('o.channel = :channel')
            ->andWhere('o.checkoutCompletedAt >= :startDate')
            ->andWhere('o.checkoutCompletedAt <= :endDate')
            ->andWhere('o.paymentState = :state')
            ->groupBy('date')
            ->setParameter('channel', $channel)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
        ;

        $result = $queryBuilder->getQuery()->getScalarResult();

        $data = [];
        foreach ($result as $item) {
            $data[$item['date']] = (int) $item['total'];
        }

        return $data;
    }
}
