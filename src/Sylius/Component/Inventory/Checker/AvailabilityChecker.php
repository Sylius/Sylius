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
use Sylius\Component\Inventory\Quantifier\QuantifierInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

/**
 * Checks availability for given stockable object.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AvailabilityChecker implements AvailabilityCheckerInterface
{
    /**
     * @var QuantifierInterface
     */
    protected $quantifier;

    /**
     * Are backorders enabled?
     *
     * @var bool
     */
    protected $backorders;

    /**
     * @param bool                $backorders
     * @param QuantifierInterface $quantifier
     */
    public function __construct(QuantifierInterface $quantifier, $backorders)
    {
        $this->quantifier = $quantifier;
        $this->backorders = (bool) $backorders;
    }

    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        return $this->isStockGreaterThan($stockable, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        return $this->isStockGreaterThan($stockable, $quantity);
    }

    /**
     * Check if stock is greater than specified amount.
     *
     * @param Stockableinterface $stockable
     * @param integer            $quantity
     */
    private function isStockGreaterThan(StockableInterface $stockable, $quantity)
    {
        if ($stockable instanceof SoftDeletableInterface && $stockable->isDeleted()) {
            return false;
        }

        if ($this->backorders || $this->isBackorderable($stockable)) {
            return true;
        }

        $onHand = $this->quantifier->getTotalOnHand($stockable);
        $onHold = $this->quantifier->getTotalOnHold($onHold);

        return 0 < ($onHand - $onHold);
    }

    /**
     * Check if the item is backorderable in any of locations.
     *
     * @param StockableInterface $stockable
     *
     * @return Boolean
     */
    private function isBackorderable(StockableInterface $stockable)
    {
        foreach ($stockable->getStockItems() as $stockItem) {
            if ($stockItem->isBackorderable()) {
                return true;
            }
        }

        return false;
    }
}
