<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use FOS\UserBundle\Model\UserInterface;
use Sylius\Bundle\CartBundle\Doctrine\ORM\CartRepository;

class OrderRepository extends CartRepository
{
    /**
     * Create user orders paginator.
     *
     * @param UserInterface $user
     * @param array         $sorting
     *
     * @return PagerfantaInterface
     */
    public function createByUserPaginator(UserInterface $user, array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilderByUser($user, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Gets orders for user.
     *
     * @param  UserInterface $user
     * @param  array         $sorting
     * @return array
     */
    public function findByUser(UserInterface $user, array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilderByUser($user, $sorting);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get the order data for the details page.
     *
     * @param integer $id
     */
    public function findForDetailsPage($id)
    {
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->leftJoin('o.adjustments', 'adjustment')
            ->leftJoin('o.user', 'user')
            ->leftJoin('o.inventoryUnits', 'inventoryUnit')
            ->leftJoin('o.shipments', 'shipment')
            ->leftJoin('shipment.method', 'shippingMethod')
            ->leftJoin('o.payment', 'payment')
            ->leftJoin('payment.method', 'paymentMethod')
            ->leftJoin('item.variant', 'variant')
            ->leftJoin('variant.images', 'image')
            ->leftJoin('variant.product', 'product')
            ->leftJoin('variant.options', 'optionValue')
            ->leftJoin('optionValue.option', 'option')
            ->leftJoin('o.billingAddress', 'billingAddress')
            ->leftJoin('billingAddress.country', 'billingCountry')
            ->leftJoin('o.shippingAddress', 'shippingAddress')
            ->leftJoin('shippingAddress.country', 'shippingCountry')
            ->addSelect('adjustment')
            ->addSelect('user')
            ->addSelect('inventoryUnit')
            ->addSelect('shipment')
            ->addSelect('shippingMethod')
            ->addSelect('payment')
            ->addSelect('paymentMethod')
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
     * @param boolean $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();
        $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('o.completedAt'));

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

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
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

    protected function getCollectionQueryBuilderByUser(UserInterface $user, array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $queryBuilder
            ->innerJoin('o.user', 'user')
            ->andWhere('user = :user')
            ->setParameter('user', $user)
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
