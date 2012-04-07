<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\SalesBundle\Model\StatusInterface;
use Sylius\Bundle\SalesBundle\Model\StatusManager as BaseStatusManager;

class StatusManager extends BaseStatusManager
{
    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Status entity repository.
     *
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityManager $entityManager
     * @param string        $class
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        parent::__construct($class);

        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function createStatus()
    {
        $class = $this->getClass();
        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    public function persistStatus(StatusInterface $status)
    {
        $this->entityManager->persist($status);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeStatus(StatusInterface $status)
    {
        $this->entityManager->remove($status);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findStatus($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findStatusBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findStatuses()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findStatusesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
}
