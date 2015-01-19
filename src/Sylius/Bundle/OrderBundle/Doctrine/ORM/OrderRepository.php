<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepository;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * Order repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderRepository extends ResourceRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findRecentOrders($amount = 10)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        $this->objectManager->getFilters()->disable('softdeleteable');

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->setMaxResults($amount)
            ->orderBy('o.completedAt', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isNumberUsed($number)
    {
        return (bool) $this->objectRepository->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.number = :number')
            ->setParameter('number', $number)
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }
}
