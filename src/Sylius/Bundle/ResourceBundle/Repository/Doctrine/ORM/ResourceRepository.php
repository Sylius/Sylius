<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Repository\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Repository\Doctrine\DoctrineResourceRepository;

/**
 * Doctrine ORM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceRepository extends DoctrineResourceRepository
{
    /**
     * {@inheritdoc}
     */
    public function get(array $criteria)
    {
        return $this
            ->getQueryBuilder($criteria, array())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection(array $criteria = array(), array $sorting = array(), $limit = null)
    {
        $queryBuilder = $this->getQueryBuilder($criteria, $sorting);

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(array $criteria = array(), array $sorting = array())
    {
        return new Pagerfanta(new DoctrineORMAdapter($this->getQueryBuilder($criteria, $sorting)));
    }

    protected function getQueryBuilder(array $criteria, array $sorting)
    {
        $queryBuilder = $this->getBasicQueryBuilder();

        $this->filter($queryBuilder, $criteria);
        $this->sort($queryBuilder, $sorting);

        return $queryBuilder;
    }

    protected function filter(QueryBuilder $queryBuilder, array $criteria)
    {
        $reflectionClass = new \ReflectionClass($this->getClass());
        $properties = array_keys($reflectionClass->getDefaultProperties());

        $i = 0;
        foreach ($criteria as $property => $condition) {
            if (!is_array($condition)) {
                $condition = array('=', $condition);
            }

            list($comparison, $value) = $condition;

            if (in_array($property, $properties) && in_array($comparison, array('=', '!=', '>', '<', '>=', '<='))) {

                $formula = sprintf("%s.%s %s :PARAM%d", $this->getAlias(), $property, $comparison, $i);
                $queryBuilder
                    ->andWhere($formula)
                    ->setParameter('PARAM'.$i, $value)
                ;

                $i++;
            }
        }
    }

    protected function sort(QueryBuilder $queryBuilder, array $sorting)
    {
        $acceptedOrders = array('asc', 'desc');

        foreach ($sorting as $property => $order) {
            if (in_array(strtolower($order), $acceptedOrders)) {
                $queryBuilder->orderBy($this->getAlias().'.'.$property, $order);
            }
        }
    }

    protected function getBasicQueryBuilder()
    {
        return $this
            ->getObjectRepository()
            ->createQueryBuilder($this->getAlias())
        ;
    }

    protected function getAlias()
    {
        return 'r';
    }
}
