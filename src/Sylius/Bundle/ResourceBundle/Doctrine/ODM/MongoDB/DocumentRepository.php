<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB;

use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Doctrine\ODM\MongoDB\DocumentRepository as BaseDocumentRepository;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Doctrine ODM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
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
            ->getIterator()
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
     * @param array|null $sorting
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function findBy(array $criteria, ?array $sorting = null, $limit = null, $offset = null)
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
            ->getIterator()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = [], array $sorting = [])
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function add(ResourceInterface $resource)
    {
        $this->dm->persist($resource);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource)
    {
        if (null !== $this->find($resource->getId())) {
            $this->dm->remove($resource);
            $this->dm->flush();
        }
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
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = [])
    {
        foreach ($criteria as $property => $value) {
            $queryBuilder->field($property)->equals($value);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = [])
    {
        foreach ($sorting as $property => $order) {
            $queryBuilder->sort($property, $order);
        }
    }
}
