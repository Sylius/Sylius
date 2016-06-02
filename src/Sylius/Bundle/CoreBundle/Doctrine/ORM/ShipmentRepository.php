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
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

class ShipmentRepository extends EntityRepository implements ShipmentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByOrderIdAndId($orderId, $id)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.order = :orderId')
            ->andWhere('o.id = :id')
            ->setParameter('orderId', $orderId)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->where('translation.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('shipmentOrder')
            ->innerJoin('o.order', 'shipmentOrder')
            ->addSelect('address')
            ->innerJoin('shipmentOrder.shippingAddress', 'address')
            ->addSelect('method')
            ->leftJoin('o.method', 'method')
            ->leftJoin('method.translations', 'method_translation')
        ;

        if (!empty($criteria['number'])) {
            $queryBuilder
                ->andWhere('shipmentOrder.number = :number')
                ->setParameter('number', $criteria['number'])
            ;
        }
        if (!empty($criteria['channel'])) {
            $queryBuilder
                ->andWhere('shipmentOrder.channel = :channel')
                ->setParameter('channel', $criteria['channel'])
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
                ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':createdAtFrom'))
                ->setParameter('createdAtFrom', date('Y-m-d 00:00:00', strtotime($criteria['createdAtFrom'])))
            ;
        }
        if (!empty($criteria['createdAtTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':createdAtTo'))
                ->setParameter('createdAtTo', date('Y-m-d 23:59:59', strtotime($criteria['createdAtTo'])))
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
}
