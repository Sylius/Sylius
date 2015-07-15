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
use Doctrine\ODM\MongoDB\DocumentRepository as BaseDocumentRepository;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Doctrine ODM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $className = $this->getClassName();

        return new $className();
    }

    /**
     * @param int $id
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
     * @param array $criteria
     * @param array $sorting
     * @param int   $limit
     * @param int   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $sorting = array(), $limit = null, $offset = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

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
    public function createPaginator(array $criteria = array(), array $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

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
     * @param array        $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = array())
    {
        foreach ($criteria as $property => $value) {
            $queryBuilder->field($property)->equals($value);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = array())
    {
        foreach ($sorting as $property => $order) {
            $queryBuilder->sort($property, $order);
        }
    }
}
