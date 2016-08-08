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
final class AvailabilityChecker implements AvailabilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        return $this->isStockSufficient($stockable, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        return !$stockable->isTracked() || $quantity <= ($stockable->getOnHand() - $stockable->getOnHold());
    }
}
