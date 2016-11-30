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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AvailabilityCheckerInterface
{
    /**
     * @param StockableInterface $stockable
     *
     * @return bool
     */
    public function isStockAvailable(StockableInterface $stockable);

    /**
     * @param StockableInterface $stockable
     * @param int $quantity
     *
     * @return bool
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity);
}
