<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Operator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Default inventory operator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryOperator implements InventoryOperatorInterface
{
    /**
     * Inventory unit manager.
     *
     * @var ObjectManager
     */
    protected $manager;

    /**
     * Inventory unit repository.
     *
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    protected $availabilityChecker;

    /**
     * Constructor.
     *
     * @param ObjectManager                $repository
     * @param ObjectRepository             $manager
     * @param AvailabilityCheckerInterface $availabilityChecker
     */
    public function __construct(ObjectManager $manager, ObjectRepository $repository, AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function increase(StockableInterface $stockable, $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1');
        }

        $stockable->setOnHand($stockable->getOnHand() + $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function decrease(StockableInterface $stockable, $quantity, $state = InventoryUnitInterface::STATE_SOLD)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1');
        }

        if (!$this->availabilityChecker->isStockSufficient($stockable, $quantity)) {
            throw new InsufficientStockException($stockable, $quantity);
        }

        $onHand = $stockable->getOnHand();
        $stockable->setOnHand(max(0, $onHand - $quantity));

        $units = $this->create($stockable, $quantity, $state);

        // Backorder units
        $i = 0;
        foreach ($units as $unit) {
            if (++$i > $onHand) {
                $unit->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
            }
        }

        return $units;
    }

    /**
     * {@inheritdoc}
     */
    public function create(StockableInterface $stockable, $quantity = 1, $state = InventoryUnitInterface::STATE_SOLD)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1');
        }

        $units = new ArrayCollection();

        for ($i = 0; $i < $quantity; $i++) {
            $inventoryUnit = $this->repository->createNew();
            $inventoryUnit->setStockable($stockable);
            $inventoryUnit->setInventoryState($state);

            $units->add($inventoryUnit);
        }

        return $units;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(InventoryUnitInterface $inventoryUnit)
    {
        $this->manager->remove($inventoryUnit);
        $this->manager->flush($inventoryUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function fillBackorders(StockableInterface $stockable)
    {
        $onHand = $stockable->getOnHand();
        if ($onHand <= 0) {
            return;
        }

        $units = $this->repository->findBy(array(
            'stockable'      => $stockable,
            'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED
        ), array('createdAt' => 'ASC'));

        foreach ($units as $unit) {
            $unit->setInventoryState(InventoryUnitInterface::STATE_SOLD);
            if (--$onHand === 0) {
                break;
            }
        }

        $stockable->setOnHand($onHand);
    }
}
