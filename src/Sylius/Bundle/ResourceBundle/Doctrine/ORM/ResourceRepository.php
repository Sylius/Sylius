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

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ResourceRepository as BaseResourceRepository;

/**
 * Doctrine ORM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceRepository extends BaseResourceRepository
{
    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = array(), array $sortBy = null)
    {
        $alias = $this->getAlias();
        $queryBuilder = $this->objectRepository->createQueryBuilder($alias);

        if (null !== $sortBy) {
            foreach ($sortBy as $property => $order) {
                $queryBuilder->orderBy($alias.'.'.$property, $order);
            }
        }

        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
    }

    protected function getAlias()
    {
        return 'r';
    }
}
