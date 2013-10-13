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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Backorders handler.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
class BackordersHandler implements BackordersHandlerInterface
{
    /**
     * Inventory unit repository.
     *
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function processBackorders($inventoryUnits)
    {
        if (!is_array($inventoryUnits) && !$inventoryUnits instanceof Collection) {
            throw new \InvalidArgumentException('Inventory units value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        if (0 === count($inventoryUnits)) {
            return;
        }

        foreach ($inventoryUnits as $inventoryUnit) {
            if (!$inventoryUnit instanceof InventoryUnitInterface) {
                throw new \InvalidArgumentException('Only InventoryUnitInterface objects can be processed.');
            }
        }

        if ($inventoryUnits instanceof Collection) {
            $stockable = $inventoryUnits->first()->getStockable();
        } else {
            $stockable = $inventoryUnits[0]->getStockable();
        }

        $onHand = $stockable->getOnHand();

        // Backorder units.
        $i = 0;

        foreach ($inventoryUnits as $inventoryUnit) {
            if ($stockable !== $inventoryUnit->getStockable()) {
                throw new \InvalidArgumentException('Do not mix the inventory units when processing backorders.');
            }
            if (++$i > $onHand) {
                $inventoryUnit->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
            }
        }
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
