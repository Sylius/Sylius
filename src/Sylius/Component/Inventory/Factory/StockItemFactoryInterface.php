<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Factory;

use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Stock item factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemFactoryInterface
{
    /**
     * Create stock items for given item and location.
     *
     * @param StockableInterface     $stockable
     * @param StockLocationInterface $location
     */
    public function create(StockableInterface $stockable, StockLocationInterface $location);

    /**
     * Create all missing stock items for stockable.
     *
     * @param StockableInterface $stockable
     */
    public function createAllForStockable(StockableInterface $stockable);

    /**
     * Create all missing stock item for location.
     *
     * @param StockLocationInterface $location
     */
    public function createAllForLocation(StockLocationInterface $location);
}
