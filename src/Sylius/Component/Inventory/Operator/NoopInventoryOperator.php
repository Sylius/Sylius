<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Operator;

use Sylius\Component\Inventory\Model\StockItemInterface;

/**
 * Inventory operator which does not adjust inventory
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class NoopInventoryOperator implements InventoryOperatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function increase(StockItemInterface $stockItem, $quantity)
    {
        // Nothing happens.
    }

    /**
     * {@inheritdoc}
     */
    public function decrease(StockItemInterface $stockItem, $quantity)
    {
        // Nothing happens.
    }

    /**
     * {@inheritdoc}
     */
    public function hold(StockItemInterface $stockItem, $quantity)
    {
        // Nothing happens.
    }

    /**
     * {@inheritdoc}
     */
    public function release(StockItemInterface $stockItem, $quantity)
    {
        // Nothing happens.
    }
}
