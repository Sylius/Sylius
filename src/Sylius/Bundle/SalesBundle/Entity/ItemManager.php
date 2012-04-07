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
use Sylius\Bundle\SalesBundle\Model\ItemInterface;
use Sylius\Bundle\SalesBundle\Model\ItemManager as BaseItemManager;

/**
 * Doctrine ORM driver item manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ItemManager extends BaseItemManager
{
    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Entity repository.
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
        $this->repository = $this->entityManager->getRepository($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function createItem()
    {
        $class = $this->getClass();

        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    public function persistItem(ItemInterface $order)
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $order)
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findItem($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findItemBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findItems()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findItemsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
}
