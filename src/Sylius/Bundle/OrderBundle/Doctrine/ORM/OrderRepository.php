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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderRepository extends EntityRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findRecentOrders($amount = 10)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->leftJoin('o.items', 'item')
            ->addSelect('item')
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
        return (bool) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.number = :number')
            ->setParameter('number', $number)
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByNumber($orderNumber)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->andWhere('o.number = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
