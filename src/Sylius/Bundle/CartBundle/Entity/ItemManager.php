<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CartBundle\Model\ItemInterface;
use Sylius\Bundle\CartBundle\Model\ItemManager as BaseItemManager;

class ItemManager extends BaseItemManager
{
    /**
     * Entity Manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Items repository.
     *
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        parent::__construct($class);

        $this->entityManager = $entityManager;
        $this->repository    = $this->entityManager->getRepository($class);
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
    public function persistItem(ItemInterface $cart)
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $cart)
    {
        $this->entityManager->remove($cart);
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
        return $this->findBy($criteria);
    }
}
