<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Doctrine ORM driver entity repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class EntityRepository extends BaseEntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $className = $this->getClassName();

        return new $className;
    }

    /**
     * @param mixed $id
     *
     * @return null|object
     */
    public function find($id)
    {
        return $this
            ->getQueryBuilder()
            ->andWhere($this->getAlias().'.id = '.intval($id))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this
            ->getCollectionQueryBuilder()
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param array $criteria
     *
     * @return null|object
     */
    public function findOneBy(array $criteria)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param array   $criteria
     * @param array   $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
    }

    /**
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
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
            if (null === $value) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->isNull($this->getPropertyName($property)));
            } elseif (!is_array($value)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($this->getPropertyName($property), ':' . $property))
                    ->setParameter($property, $value);
            } else {
                $queryBuilder->andWhere($queryBuilder->expr()->in($this->getPropertyName($property), $value));
            }
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
            if (!empty($order)) {
                $queryBuilder->orderBy($this->getPropertyName($property), $order);
            }
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return $this->getAlias().'.'.$name;
        }

        return $name;
    }

    protected function getAlias()
    {
        return 'o';
    }
}
