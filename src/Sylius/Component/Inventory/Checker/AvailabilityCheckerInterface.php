<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Checker;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Stock availability checker interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AvailabilityCheckerInterface
{
    /**
     * Checks whether stockable object is available in stock.
     * This method should not care about what quantity is required.
     *
     * @param StockableInterface $stockable
     *
     * @return bool
     */
    public function isStockAvailable(StockableInterface $stockable);

    /**
     * Checks whether stockable object is available in stock.
     * Takes required quantity into account.
     *
     * @param StockableInterface $stockable
     * @param int            $quantity
     *
     * @return bool
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity);
}
