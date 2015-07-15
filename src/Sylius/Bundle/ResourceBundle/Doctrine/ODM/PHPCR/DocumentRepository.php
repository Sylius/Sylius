<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Pagerfanta\Adapter\DoctrineODMPhpcrAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Doctrine PHPCR-ODM driver document repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author David Buchmann <mail@davidbu.ch>
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
        return new Pagerfanta(new DoctrineODMPhpcrAdapter($queryBuilder));
    }

    /**
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = array())
    {
        foreach ($criteria as $property => $value) {
            if (!empty($value)) {
                $queryBuilder
                    ->andWhere()
                        ->eq()
                            ->field($this->getPropertyName($property))
                            ->literal($value);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = array())
    {
        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->orderBy()->{$order}()->field('o.'.$property);
            }
        }

        $queryBuilder->end();
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

    /**
     * @return string
     */
    protected function getAlias()
    {
        return 'o';
    }
}
