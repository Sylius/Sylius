<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Inventory\Checker;

use Sylius\Component\Inventory\Model\StockableInterface;

interface AvailabilityCheckerInterface
{
    /**
     * @param StockableInterface $stockable
     *
     * @return bool
     */
    public function isStockAvailable(StockableInterface $stockable): bool;

    /**
     * @param StockableInterface $stockable
     * @param int $quantity
     *
     * @return bool
     */
    public function isStockSufficient(StockableInterface $stockable, int $quantity): bool;
}
