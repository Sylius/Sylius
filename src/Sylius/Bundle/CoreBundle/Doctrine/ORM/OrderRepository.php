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
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CartRepository;
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class OrderRepository extends CartRepository implements OrderRepositoryInterface
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
    public function createPaginatorByCustomer(CustomerInterface $customer, array $sorting = [])
    {
        $queryBuilder = $this->createByCustomerQueryBuilder($customer);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
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
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->leftJoin('o.adjustments', 'adjustment')
            ->leftJoin('o.customer', 'customer')
            ->leftJoin('o.items', 'item')
            ->leftJoin('item.units', 'itemUnit')
            ->leftJoin('o.shipments', 'shipment')
            ->leftJoin('shipment.method', 'shippingMethod')
            ->leftJoin('o.payments', 'payments')
            ->leftJoin('payments.method', 'paymentMethods')
            ->leftJoin('item.variant', 'variant')
            ->leftJoin('variant.images', 'image')
            ->leftJoin('variant.object', 'product')
            ->leftJoin('variant.options', 'optionValue')
            ->leftJoin('optionValue.option', 'option')
            ->leftJoin('o.billingAddress', 'billingAddress')
            ->leftJoin('o.shippingAddress', 'shippingAddress')
            ->addSelect('item')
            ->addSelect('adjustment')
            ->addSelect('customer')
            ->addSelect('itemUnit')
            ->addSelect('shipment')
            ->addSelect('shippingMethod')
            ->addSelect('payments')
            ->addSelect('paymentMethods')
            ->addSelect('variant')
            ->addSelect('image')
            ->addSelect('product')
            ->addSelect('option')
            ->addSelect('optionValue')
            ->addSelect('billingAddress')
            ->addSelect('shippingAddress')
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
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
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->leftJoin('o.customer', 'customer')
            ->addSelect('customer')
        ;

        if (!empty($criteria['number'])) {
            $queryBuilder
                ->andWhere('o.number = :number')
                ->setParameter('number', $criteria['number'])
            ;
        }
        if (!empty($criteria['totalFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('o.total', ':totalFrom'))
                ->setParameter('totalFrom', $criteria['totalFrom'] * 100)
            ;
        }
        if (!empty($criteria['totalTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('o.total', ':totalTo'))
                ->setParameter('totalTo', $criteria['totalTo'] * 100)
            ;
        }
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
        if (!empty($criteria['paymentState'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('o.paymentState', ':paymentState'))
                ->setParameter('paymentState', $criteria['paymentState'])
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
    public function countByCustomerAndCoupon(CustomerInterface $customer, CouponInterface $coupon)
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
    public function findBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->createQueryBuilderBetweenDates($from, $to, $state);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->createQueryBuilderBetweenDates($from, $to, $state);

        return (int) $queryBuilder
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function revenueBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->createQueryBuilderBetweenDates($from, $to, $state);

        return (int)$queryBuilder
            ->select('sum(o.total)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findExpired(\DateTime $expiresAt, $state = OrderInterface::STATE_NEW)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->leftJoin('o.items', 'item')
            ->addSelect('item')
        ;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->lt('o.expiresAt', ':expiresAt'))
            ->andWhere('o.state = :state')
            ->setParameter('expiresAt', $expiresAt)
            ->setParameter('state', $state)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findCompleted(array $sorting = [], $limit = 5)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'));

        $this->applySorting($queryBuilder, $sorting);

        return $queryBuilder
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
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
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $state
     *
     * @return QueryBuilder
     */
    private function createQueryBuilderBetweenDates(\DateTime $from, \DateTime $to, $state)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if (null !== $state) {
            $queryBuilder->andWhere('o.state = :state')->setParameter('state', $state);
        }

        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;

        return $queryBuilder;
    }
}
