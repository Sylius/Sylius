<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\User\Repository\CustomerRepositoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerRepository extends EntityRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatest($count)
    {
        return $this->createQueryBuilder('o')
            ->addOrderBy('o.createdAt', 'desc')
            ->setMaxResults($count)
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
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->leftJoin($this->getPropertyName('user'), 'user');

        if (isset($criteria['query'])) {
            $queryBuilder
                ->where($queryBuilder->expr()->like($this->getPropertyName('emailCanonical'), ':query'))
                ->orWhere($queryBuilder->expr()->like($this->getPropertyName('firstName'), ':query'))
                ->orWhere($queryBuilder->expr()->like($this->getPropertyName('lastName'), ':query'))
                ->orWhere($queryBuilder->expr()->like('user.username', ':query'))
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }
        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('user.enabled', ':enabled'))
                ->setParameter('enabled', $criteria['enabled'])
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
