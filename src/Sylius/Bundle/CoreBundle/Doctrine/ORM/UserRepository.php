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

use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;
use Sylius\Component\Core\Repository\UserRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class UserRepository extends BaseUserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if (isset($criteria['query'])) {
            $queryBuilder
                ->leftJoin('o.customer', 'customer')
                ->where('customer.emailCanonical LIKE :query')
                ->orWhere('customer.firstName LIKE :query')
                ->orWhere('customer.lastName LIKE :query')
                ->orWhere('o.username LIKE :query')
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }
        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere('o.enabled = :enabled')
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

    /**
     * {@inheritdoc}
     */
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->leftJoin('o.customer', 'customer')
            ->addSelect('customer')
            ->where($queryBuilder->expr()->eq('o.id', ':id'))
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
    public function findOneByEmail($email)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->leftJoin('o.customer', 'customer')
            ->andWhere($queryBuilder->expr()->eq('customer.emailCanonical', ':email'))
            ->setParameter('email', $email)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
