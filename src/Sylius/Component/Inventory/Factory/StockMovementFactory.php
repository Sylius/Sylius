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
use Sylius\Component\Inventory\Model\StockMovementInterface;
use Sylius\Component\Inventory\Repository\StockMovementRepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementFactory extends Factory implements StockMovementFactoryInterface
{
    /**
     * @var StockMovementRepositoryInterface
     */
    protected $stockMovementRepository;

    /**
     * @param StockMovementRepositoryInterface $stockMovementRepository
     */
    public function __construct($classname, StockMovementRepositoryInterface $stockMovementRepository)
    {
        parent::__construct($classname);

        $this->stockMovementRepository = $stockMovementRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createForStockItem(StockItemInterface $stockItem, $quantity)
    {
        if (!is_integer($quantity) || 0 === $quantity) {
            throw new \InvalidArgumentException('Invalid quantity given!');
        }

        /** @var StockMovementInterface movement */
        $movement = $this->createNew();

        $movement->setStockItem($stockItem);
        $movement->setQuantity($quantity);

        $stockItem->addStockMovement($movement);

        return $movement;
    }
}
