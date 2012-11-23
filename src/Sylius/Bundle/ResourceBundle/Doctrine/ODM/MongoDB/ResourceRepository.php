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

use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
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
        $queryBuilder = $this->objectRepository->createQueryBuilder();
        if (null !== $sortBy) {
            foreach ($sortBy as $property => $order) {
                $queryBuilder->sort($property, $order);
            }
        }
    
        return new Pagerfanta(new DoctrineODMMongoDBAdapter($queryBuilder));
    }
}
