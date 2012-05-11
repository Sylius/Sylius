<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitManager as BaseInventoryUnitManager;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * ORM driver inventory unit manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnitManager extends BaseInventoryUnitManager
{
    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Inventory unit entity repository.
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
    public function createInventoryUnit(StockableInterface $stockable)
    {
        $class = $this->getClass();

        $inventoryUnit = new $class;
        $inventoryUnit->setStockableId($stockable->getStockableId());

        return $inventoryUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function persistInventoryUnit(InventoryUnitInterface $inventoryUnit)
    {
        $this->entityManager->persist($inventoryUnit);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeInventoryUnit(InventoryUnitInterface $inventoryUnit)
    {
        $this->entityManager->remove($inventoryUnit);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findInventoryUnit($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findInventoryUnitBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findInventoryUnits()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findInventoryUnitsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function countInventoryUnitsBy(StockableInterface $stockable, array $criteria)
    {
        $queryBuilder = $this->repository
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.stockableId = :stockableId')
            ->setParameter('stockableId', $stockable->getStockableId())
        ;

        foreach ($criteria as $parameter => $value) {
            $queryBuilder
                ->andWhere(sprintf('c.%s = :%s', $parameter))
                ->setParameter($parameter, $value)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
