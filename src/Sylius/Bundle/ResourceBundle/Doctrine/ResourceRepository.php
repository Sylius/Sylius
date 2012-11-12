<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Repository\ResourceRepository as BaseResourceRepository;

/**
 * Base Doctrine resource manager class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class ResourceRepository extends BaseResourceRepository
{
    protected $objectRepository;

    public function __construct(ObjectRepository $objectRepository, $className)
    {
        $this->objectRepository = $objectRepository;

        parent::__construct($className);
    }

    public function find($id)
    {
        return $this->objectRepository->find($id);
    }

    public function findAll()
    {
        return $this->objectRepository->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->objectRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria)
    {
        return $this->objectRepository->findBy($criteria);
    }

}
