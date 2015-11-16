<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB;

use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ResourceRepository as BaseResourceRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Doctrine ODM driver resource repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceRepository extends BaseResourceRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = array(), array $sortBy = null)
    {
        $queryBuilder = $this->createQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sortBy);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return new Pagerfanta(new DoctrineODMMongoDBAdapter($queryBuilder));
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->field($property)->equals($value)
            ;
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = null)
    {
        if (null === $sorting) {
            return;
        }

        foreach ($sorting as $property => $order) {
            $queryBuilder->sort($property, $order);
        }
    }
}
