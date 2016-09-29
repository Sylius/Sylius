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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->addSelect('customer')
            ->leftJoin('o.customer', 'customer')
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createByCustomerQueryBuilder(CustomerInterface $customer)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->innerJoin('o.customer', 'customer')
            ->andWhere('customer = :customer')
            ->setParameter('customer', $customer)
        ;

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findByCustomer(CustomerInterface $customer, array $sorting = [])
    {
        $queryBuilder = $this->createByCustomerQueryBuilder($customer);
        $this->applySorting($queryBuilder, $sorting);

        return $queryBuilder
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
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->leftJoin('o.items', 'item')
            ->innerJoin('o.promotionCoupon', 'coupon')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.completedAt IS NOT NULL')
            ->andWhere('coupon = :coupon')
            ->setParameter('customer', $customer)
            ->setParameter('coupon', $coupon)
        ;

        $count = (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function createCheckoutsPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('o.completedAt'));

        if (!empty($criteria['createdAtFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':createdAtFrom'))
                ->setParameter('createdAtFrom', $criteria['createdAtFrom'])
            ;
        }
        if (!empty($criteria['createdAtTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':createdAtTo'))
                ->setParameter('createdAtTo', $criteria['createdAtTo'])
            ;
        }
        if (!empty($criteria['channel'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('o.channel', ':channel'))
                ->setParameter('channel', $criteria['channel'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = [];
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomer(CustomerInterface $customer)
    {
       return (int) $this->createByCustomerQueryBuilder($customer)
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByNumberAndCustomer($number, CustomerInterface $customer)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.customer', 'customer')
            ->andWhere('customer = :customer')
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
    public function findCartByIdAndChannel($id, ChannelInterface $channel)
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
}
