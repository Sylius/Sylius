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
 * Checks availability for given stockable object.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
class AvailabilityChecker implements AvailabilityCheckerInterface
{
    /**
     * Are backorders enabled?
     *
     * @var Boolean
     */
    protected $backorders;

    /**
     * Constructor.
     *
     * @param Boolean $backorders
     */
    public function __construct($backorders)
    {
        $this->backorders = (Boolean) $backorders;
    }

    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        if (true === $this->backorders || $stockable->isAvailableOnDemand()) {
            return true;
        }

        return 0 < $stockable->getOnHand();
    }

    /**
     * {@inheritdoc}
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        if (true === $this->backorders || $stockable->isAvailableOnDemand()) {
            return true;
        }

        return $quantity <= $stockable->getOnHand();
    }
}
