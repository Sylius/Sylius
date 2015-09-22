<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Backorders;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Inventory\Model\InventoryUnit;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Default backorders filler
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BackordersFiller implements BackordersFillterInterface
{
    /**
     * @var InventoryOperatorInterface
     */
    protected $operator;

    /**
     * Inventory unit repository.
     *
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param InventoryOperatorInterface $operator
     * @param ObjectRepository           $repository
     */
    public function __construct(InventoryOperatorInterface $operator, ObjectRepository $repository)
    {
        $this->operator = $operator;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function fillBackorders(StockItemInterface $stockItem)
    {
        $onHand = $stockItem->getOnHand();

        if ($onHand <= 0) {
            return;
        }

        $units = $this->repository->findBy(array(
            'stockable'      => $stockable,
            'stockLocation'  => $stockItem->getLocation(),
            'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED
        ), array('createdAt' => 'ASC'));

        $filled = 0;

        foreach ($units as $unit) {
            $unit->setInventoryState(InventoryUnitInterface::STATE_SOLD);
            $filled++;

            if (--$onHand === 0) {
                break;
            }
        }

        $this->operator->decrease($stockItem, $filled);
    }
}
