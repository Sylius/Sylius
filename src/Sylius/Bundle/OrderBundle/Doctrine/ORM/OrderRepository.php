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
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderRepository extends EntityRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return (int) $queryBuilder
            ->select('COUNT(o.id)')
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSales()
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return (int) $queryBuilder
            ->select('SUM(o.total)')
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatest($count)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->addSelect('item')
            ->leftJoin('o.items', 'item')
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->setMaxResults($count)
            ->orderBy('o.completedAt', 'desc')
            ->getQuery()
            ->getResult()
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
    /**
     * {@inheritdoc}
     */
    public function findCartById($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findCartsNotModifiedSince(\DateTime $terminalDate)
    {
        return $this->createQueryBuilder('o')
            ->where('o.state = :state')
            ->andWhere('o.updatedAt < :terminalDate')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('terminalDate', $terminalDate)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrdersUnpaidSince(\DateTime $terminalDate)
    {
        return $this->createQueryBuilder('o')
            ->where('o.checkoutState = :checkoutState')
            ->andWhere('o.paymentState != :paymentState')
            ->andWhere('o.completedAt < :terminalDate')
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('paymentState', OrderPaymentStates::STATE_PAID)
            ->setParameter('terminalDate', $terminalDate)
            ->getQuery()
            ->getResult()
        ;
    }
}
