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

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Doctrine\ODM\PHPCR\Query\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineODMPhpcrAdapter;

/**
 * Doctrine PHPCR-ODM driver document repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author David Buchmann <mail@davidbu.ch>
 */
class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    public function createNew()
    {
        $className = $this->getClassName();

        return new $className;
    }

    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        return $this->getPaginator($queryBuilder);
    }

    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return new Pagerfanta(new DoctrineODMPhpcrAdapter($queryBuilder));
    }

    protected function getCollectionQueryBuilder()
    {
        return $this->createQueryBuilder();
    }

    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            if (!empty($value)) {
                $queryBuilder
                    ->andWhere($this->getPropertyName($property).' = :'.$property)
                    ->setParameter($property, $value)
                ;
            }
        }
    }

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
