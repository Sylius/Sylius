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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ShipmentRepository extends EntityRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array())
    {
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getCollectionQueryBuilder();

        $queryBuilder
            ->leftJoin($this->getAlias().'.order', 'shipmentOrder')
            ->leftJoin('shipmentOrder.shippingAddress', 'address')
            ->addSelect('shipmentOrder')
            ->addSelect('address')
        ;

        if (!empty($criteria['number'])) {
            $queryBuilder
                ->andWhere('shipmentOrder.number = :number')
                ->setParameter('number', $criteria['number'])
            ;
        }
        if (!empty($criteria['shippingAddress'])) {
            $queryBuilder
                ->andWhere('address.lastName LIKE :shippingAddress')
                ->setParameter('shippingAddress', '%'.$criteria['shippingAddress'].'%')
            ;
        }
        if (!empty($criteria['createdAtFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte($this->getAlias().'.createdAt', ':createdAtFrom'))
                ->setParameter('createdAtFrom', date('Y-m-d 00:00:00',strtotime($criteria['createdAtFrom'])))
            ;
        }
        if (!empty($criteria['createdAtTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte($this->getAlias().'.createdAt', ':createdAtTo'))
                ->setParameter('createdAtTo', date('Y-m-d 23:59:59', strtotime($criteria['createdAtTo'])))
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

    protected function getAlias()
    {
        return 's';
    }
}
