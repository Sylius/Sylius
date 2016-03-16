<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Templating\Helper;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Templating\Helper\Helper;

class InventoryHelper extends Helper
{
    /**
     * Availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    private $checker;

    /**
     * Constructor.
     *
     * @param AvailabilityCheckerInterface $checker
     */
    public function __construct(AvailabilityCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Check whether stockable is in stock or not.
     *
     * @param StockableInterface $stockable
     *
     * @return bool
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        return $this->checker->isStockAvailable($stockable);
    }

    /**
     * Check whether stock is sufficient for given
     * stockable and quantity.
     *
     * @param StockableInterface $stockable
     * @param int            $quantity
     *
     * @return bool
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        return $this->checker->isStockSufficient($stockable, $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_inventory';
    }
}
