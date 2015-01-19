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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Base Doctrine resource repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class ResourceRepository implements ResourceRepositoryInterface
{
    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectRepository $objectRepository
     * @param ObjectManager $objectManager
     */
    function __construct(ObjectRepository $objectRepository, ObjectManager $objectManager)
    {
        $this->objectRepository = $objectRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->objectRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->objectRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->objectRepository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->objectRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function enableFilter($name)
    {
        $this->objectManager->getFilters()->enable($name);
    }

    /**
     * {@inheritdoc}
     */
    public function disableFilter($name)
    {
        $this->objectManager->getFilters()->disable($name);
    }
}
