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

use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Pagerfanta\Adapter\DoctrineODMPhpcrAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ResourceRepository as BaseResourceRepository;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Doctrine PHPCR-ODM driver document repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author David Buchmann <mail@davidbu.ch>
 */
class ResourceRepository extends BaseResourceRepository implements ResourceRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

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
            return 'o.'.$name;
        }

        return $name;
    }
}
