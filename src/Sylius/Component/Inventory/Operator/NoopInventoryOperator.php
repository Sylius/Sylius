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

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

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
    public function increase(StockableInterface $stockItem, $quantity)
    {
        // nothing happens.
    }

    /**
     * {@inheritdoc}
     */
    public function decrease($inventoryUnits)
    {
        // nothing happens.
    }

    /**
     * {@inheritdoc}
     */
    public function hold(ProductVariantInterface $variant, $quantity)
    {
        // nothing happens
    }

    /**
     * {@inheritdoc}
     */
    public function release(ProductVariantInterface $variant, $quantity)
    {
        // nothing happens
    }
}
