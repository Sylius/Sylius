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

final class AvailabilityChecker implements AvailabilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable): bool
    {
        return $this->isStockSufficient($stockable, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isStockSufficient(StockableInterface $stockable, int $quantity): bool
    {
        return !$stockable->isTracked() || $quantity <= ($stockable->getOnHand() - $stockable->getOnHold());
    }
}
