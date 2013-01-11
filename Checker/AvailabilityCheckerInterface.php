<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Checker;

use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Stock availability checker interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
interface AvailabilityCheckerInterface
{
    /**
     * Checks whether stockable object is available in stock.
     * This method should not care about what quantity is required.
     *
     * @param StockableInterface $stockable
     *
     * @return Boolean
     */
    public function isStockAvailable(StockableInterface $stockable);

    /**
     * Checks whether stockable object is available in stock.
     * Takes required quantity into account.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     *
     * @return Boolean
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity);
}
