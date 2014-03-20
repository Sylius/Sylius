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

use Doctrine\ODM\MongoDB\DocumentRepository as BaseDocumentRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Doctrine ODM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
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
     * @param integer $id
     *
     * @return object
     */
    public function find($id)
    {
        return $this
            ->getQueryBuilder()
            ->field('id')->equals(new \MongoId($id))
            ->getQuery()
            ->getSingleResult()
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
            ->execute()
        ;
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findOneBy(array $criteria)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);

        return $queryBuilder
            ->getQuery()
            ->getSingleResult()
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
            $queryBuilder->limit($limit);
        }

        if (null !== $offset) {
            $queryBuilder->skip($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = array(), array $sortBy = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

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
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder();
    }

    /**
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilder()
    {
        return $this->createQueryBuilder();
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
