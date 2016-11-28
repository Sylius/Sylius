<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        return $this->createQueryBuilder('o')
            ->addSelect('channel')
            ->leftJoin('o.channel', 'channel')
            ->addSelect('customer')
            ->leftJoin('o.customer', 'customer')
            ->andWhere('o.state != :state')
            ->setParameter('state', OrderInterface::STATE_CART)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createByCustomerIdQueryBuilder($customerId)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.customer', 'customer')
            ->andWhere('customer.id = :customerId')
            ->andWhere('o.state != :state')
            ->setParameter('customerId', $customerId)
            ->setParameter('state', OrderInterface::STATE_CART)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByCustomer(CustomerInterface $customer)
    {
        return $this->createByCustomerIdQueryBuilder($customer->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneForPayment($id)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.payments', 'payments')
            ->leftJoin('payments.method', 'paymentMethods')
            ->addSelect('payments')
            ->addSelect('paymentMethods')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomerAndCoupon(CustomerInterface $customer, PromotionCouponInterface $coupon)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $count = $queryBuilder
            ->select('count(o.id)')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.promotionCoupon = :coupon')
            ->andWhere($queryBuilder->expr()->notIn('o.state', ':states'))
            ->setParameter('customer', $customer)
            ->setParameter('coupon', $coupon)
            ->setParameter('states', [OrderInterface::STATE_CART, OrderInterface::STATE_CANCELLED])
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $count;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomer(CustomerInterface $customer)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $count = $queryBuilder
            ->select('count(o.id)')
            ->andWhere('o.customer = :customer')
            ->andWhere($queryBuilder->expr()->notIn('o.state', ':states'))
            ->setParameter('customer', $customer)
            ->setParameter('states', [OrderInterface::STATE_CART, OrderInterface::STATE_CANCELLED])
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $count;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByNumberAndCustomer($number, CustomerInterface $customer)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.number = :number')
            ->setParameter('customer', $customer)
            ->setParameter('number', $number)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findCartByChannel($id, ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->andWhere('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findCartByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer)
    {
        return $this->createQueryBuilder('o')
            ->where('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.customer = :customer')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('channel', $channel)
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalSalesForChannel(ChannelInterface $channel)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $total = $queryBuilder
            ->select('SUM(o.total)')
            ->andWhere('o.channel = :channel')
            ->andWhere($queryBuilder->expr()->notIn('o.state', ':states'))
            ->setParameter('channel', $channel)
            ->setParameter('states', [OrderInterface::STATE_CART, OrderInterface::STATE_CANCELLED])
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $total;
    }

    /**
     * {@inheritDoc}
     */
    public function countByChannel(ChannelInterface $channel)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $count = $queryBuilder
            ->select('COUNT(o.id)')
            ->andWhere('o.channel = :channel')
            ->andWhere($queryBuilder->expr()->notIn('o.state', ':states'))
            ->setParameter('channel', $channel)
            ->setParameter('states', [OrderInterface::STATE_CART, OrderInterface::STATE_CANCELLED])
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $count;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestInChannel($count, ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.state != :state')
            ->setMaxResults($count)
            ->orderBy('o.checkoutCompletedAt', 'desc')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->getQuery()
            ->getResult()
        ;
    }
}
