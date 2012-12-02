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
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Doctrine ORM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DocumentRepository extends BaseDocumentRepository
{
    public function createNew()
    {
        $className = $this->getClassName();

        return new $className;
    }

    public function createPaginator(array $criteria = array(), array $sortBy = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        if (null !== $sortBy) {
            foreach ($sortBy as $property => $order) {
                $queryBuilder->sort($property, $order);
            }
        }

        return new Pagerfanta(new DoctrineODMMongoDBAdapter($queryBuilder));
    }
}
