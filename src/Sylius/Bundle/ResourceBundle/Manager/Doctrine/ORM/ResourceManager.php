<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Manager\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Manager\Doctrine\DoctrineResourceManager;

/**
 * Doctrine ORM driver resource manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceManager extends DoctrineResourceManager
{
    public function createPaginator()
    {
        return new Pagerfanta(new DoctrineORMAdapter($this->objectRepository->createQueryBuilder('r')));
    }
}
