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
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\SalesBundle\Filtering\FilterInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\SalesBundle\Model\OrderManager as BaseOrderManager;
use Sylius\Bundle\SalesBundle\Sorting\SorterInterface;

/**
 * Order manager for Doctrine ORM driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderManager extends BaseOrderManager
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
    public function createOrder()
    {
        $class = $this->getClass();

        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(SorterInterface $sorter = null, FilterInterface $filter = null)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from($this->class, 'o')
            ->orderBy('o.createdAt', 'DESC');

        if (null != $filter) {
            $filter->filter($queryBuilder);
        }

        if (null != $sorter) {
            $sorter->sort($queryBuilder);
        }

        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder->getQuery()));
    }

    /**
     * {@inheritdoc}
     */
    public function persistOrder(OrderInterface $order)
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrder(OrderInterface $order)
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findOrder($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrderBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrders()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findOrdersBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
}
