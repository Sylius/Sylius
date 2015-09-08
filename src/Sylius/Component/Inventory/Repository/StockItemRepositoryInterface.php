<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Repository;

use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * This interface should be implemented by repository of stock items.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemRepositoryInterface extends RepositoryInterface
{
    /**
     * Count total on hand by stockable.
     *
     * @param StockableInterface $stockable
     *
     * @return integer
     */
    public function countOnHandByStockable(StockableInterface $stockable);

    /**
     * Count total on hold by stockable.
     *
     * @param StockableInterface $stockable
     *
     * @return integer
     */
    public function countOnHoldByStockable(StockableInterface $stockable);

    /**
     * Find a stock item for given stockable and location.
     *
     * @param StockableInterface     $stockable
     * @param StockLocationInterface $stockLocation
     *
     * @return null|StockItemInterface
     */
    public function findByStockableAndLocation(StockableInterface $stockable, StockLocationInterface $location);

    /**
     * Create paginator for given location.
     *
     * @param integer $locationId
     *
     * @return PagerfantaInterface
     */
    public function createByLocationPaginator($locationId);
}
