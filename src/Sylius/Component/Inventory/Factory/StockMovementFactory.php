<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Factory;

use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementFactory implements StockMovementFactoryInterface
{
    /**
     * @var StockMovementRepositoryInterface
     */
    protected $stockMovementRepository;

    /**
     * @param RepositoryInterface $stockMovementRepository
     */
    public function __construct(
        RepositoryInterface $stockMovementRepository
    )
    {
        $this->stockMovementRepository = $stockMovementRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create(StockItemInterface $stockItem, $quantity)
    {
        if (!is_integer($quantity) || 0 === $quantity) {
            throw new \InvalidArgumentException('Invalid quantity given!');
        }

        $movement = $this->stockMovementRepository->createNew();

        $movement->setStockItem($stockItem);
        $movement->setQuantity($quantity);

        $stockItem->addStockMovement($movement);

        return $movement;
    }
}
