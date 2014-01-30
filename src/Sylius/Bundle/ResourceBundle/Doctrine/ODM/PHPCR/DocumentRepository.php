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
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

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
        return $this->createQueryBuilder('o');
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
                $queryBuilder->orderBy()->{$order}()->field('o.'.$property);
            }
        }

        $queryBuilder->end();
    }

    protected function getAlias()
    {
        return 'o';
    }
}
