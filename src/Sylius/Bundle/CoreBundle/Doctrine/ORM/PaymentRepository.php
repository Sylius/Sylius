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

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

class PaymentRepository extends EntityRepository implements PaymentRepositoryInterface
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
     * @param array $criteria
     * @param array $sorting
     *
     * @return Pagerfanta
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('paymentOrder')
            ->leftJoin('o.order', 'paymentOrder')
            ->addSelect('address')
            ->leftJoin('paymentOrder.billingAddress', 'address')
            ->addSelect('method')
            ->leftJoin('o.method', 'method')
            ->leftJoin('method.translations', 'method_translation')
        ;

        if (!empty($criteria['number'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('paymentOrder.number', ':number'))
                ->setParameter('number', $criteria['number'])
            ;
        }
        if (!empty($criteria['channel'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('paymentOrder.channel', ':channel'))
                ->setParameter('channel', $criteria['channel'])
            ;
        }
        if (!empty($criteria['billingAddress'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('address.lastName', ':billingAddress'))
                ->setParameter('billingAddress', '%'.$criteria['billingAddress'].'%')
            ;
        }
        if (!empty($criteria['createdAtFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte($this->getPropertyName('createdAt'), ':createdAtFrom'))
                ->setParameter('createdAtFrom', date('Y-m-d 00:00:00', strtotime($criteria['createdAtFrom'])))
            ;
        }
        if (!empty($criteria['createdAtTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte($this->getPropertyName('createdAt'), ':createdAtTo'))
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
