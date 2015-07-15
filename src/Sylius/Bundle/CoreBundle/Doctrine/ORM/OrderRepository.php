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

use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\CartBundle\Doctrine\ORM\CartRepository;
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class OrderRepository extends CartRepository implements OrderRepositoryInterface
{
    /**
     * Create customer orders paginator.
     *
     * @param CustomerInterface $customer
     * @param array         $sorting
     *
     * @return PagerfantaInterface
     */
    public function createByCustomerPaginator(CustomerInterface $customer, array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilderByCustomer($customer, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Gets orders for customer.
     *
     * @param CustomerInterface $customer
     * @param array         $sorting
     *
     * @return array
     */
    public function findByCustomer(CustomerInterface $customer, array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilderByCustomer($customer, $sorting);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get the order data for the details page.
     *
     * @param integer $id
     *
     * @return OrderInterface|null
     */
    public function findForDetailsPage($id)
    {
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->leftJoin('o.adjustments', 'adjustment')
            ->leftJoin('o.customer', 'customer')
            ->leftJoin('item.inventoryUnits', 'inventoryUnit')
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
            ->leftJoin('billingAddress.country', 'billingCountry')
            ->leftJoin('o.shippingAddress', 'shippingAddress')
            ->leftJoin('shippingAddress.country', 'shippingCountry')
            ->addSelect('adjustment')
            ->addSelect('customer')
            ->addSelect('inventoryUnit')
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
            ->addSelect('billingCountry')
            ->addSelect('shippingAddress')
            ->addSelect('shippingCountry')
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Create filter paginator.
     *
     * @param array   $criteria
     * @param array   $sorting
     * @param Boolean $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();
        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->leftJoin('o.customer', 'customer')
            ->addSelect('customer')
        ;

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

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
                $sorting = array();
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
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('count(o.id)')
            ->innerJoin('o.promotionCoupons', 'coupons')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.completedAt IS NOT NULL')
            ->andWhere('coupons = :coupon')
            ->setParameter('customer', $customer)
            ->setParameter('coupon', $coupon)
        ;

        $count = (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->_em->getFilters()->enable('softdeleteable');

        return $count;
    }

    /**
     * Create checkouts paginator.
     *
     * @param array   $criteria
     * @param array   $sorting
     * @param Boolean $deleted
     *
     * @return PagerfantaInterface
     */
    public function createCheckoutsPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('o.completedAt'));

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
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
        if (!empty($criteria['channel'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('o.channel', ':channel'))
                ->setParameter('channel', $criteria['channel'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomerAndPaymentState(CustomerInterface $customer, $state)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('count(o.id)')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.paymentState = :state')
            ->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'))
            ->setParameter('customer', $customer)
            ->setParameter('state', $state)
        ;

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to, $state);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    public function countBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to, $state);

        return $queryBuilder
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function revenueBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to, $state);

        return $queryBuilder
            ->select('sum(o.total)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function revenueBetweenDatesGroupByDate(array $configuration = array())
    {
        $groupBy = '';
        foreach ($configuration['groupBy'] as $groupByArray) {
            $groupBy = $groupByArray.'(date)'.' '.$groupBy;
        }
        $groupBy = substr($groupBy, 0, -1);
        $groupBy = str_replace(' ', ', ', $groupBy);

        $queryBuilder = $this->getQueryBuilderBetweenDatesGroupByDate(
            $configuration['start'],
            $configuration['end'],
            $groupBy);

        $queryBuilder
            ->select('DATE(o.completed_at) as date', 'TRUNCATE(SUM(o.total)/ 100,2) as "total sum"')
        ;

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function ordersBetweenDatesGroupByDate(array $configuration = array())
    {
        $groupBy = '';

        foreach ($configuration['groupBy'] as $groupByElement) {
            $groupBy = $groupByElement.'(date)'.' '.$groupBy;
        }

        $groupBy = substr($groupBy, 0, -1);
        $groupBy = str_replace(' ', ', ', $groupBy);

        $queryBuilder = $this->getQueryBuilderBetweenDatesGroupByDate(
            $configuration['start'],
            $configuration['end'],
            $groupBy);

        $queryBuilder
            ->select('DATE(o.completed_at) as date', 'COUNT(o.id) as "Number of orders"')
        ;

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

    protected function getQueryBuilderBetweenDatesGroupByDate(\DateTime $from, \DateTime $to, $groupBy = 'Date(date)')
    {
        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder();

        return $queryBuilder
            ->from($this->getClassMetadata($this->_entityName)->getTableName(), 'o')
            ->where($queryBuilder->expr()->gte('o.completed_at', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.completed_at', ':to'))
            ->setParameter('from', $from->format('Y-m-d H:i:s'))
            ->setParameter('to', $to->format('Y-m-d H:i:s'))
            ->groupBy($groupBy)
            ->orderBy($groupBy)
        ;
    }

    public function findExpired(\DateTime $expiresAt, $state = OrderInterface::STATE_PENDING)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->lt($this->getAlias().'.updatedAt', ':expiresAt'))
            ->andWhere($this->getAlias().'.state = :state')
            ->setParameter('expiresAt', $expiresAt)
            ->setParameter('state', $state)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to, $state = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();
        if (null !== $state) {
            $queryBuilder
                ->andWhere('o.state = :state')
                ->setParameter('state', $state)
            ;
        }

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
    }

    protected function getCollectionQueryBuilderByCustomer(CustomerInterface $customer, array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $queryBuilder
            ->innerJoin('o.customer', 'customer')
            ->andWhere('customer = :customer')
            ->setParameter('customer', $customer)
        ;

        $this->applySorting($queryBuilder, $sorting);

        return $queryBuilder;
    }

    protected function getCollectionQueryBuilder()
    {
        $queryBuilder = parent::getCollectionQueryBuilder();

        return $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'));
    }
}
