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

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Interface for stock location repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockLocationRepositoryInterface extends RepositoryInterface
{
    /**
     * Find all enabled stock locations.
     *
     * @return StockLocationInterface[]
     */
    public function findAllEnabled();

    /**
     * Count locations where this stockable is backorderable.
     *
     * @param StockableInterface $stockable
     *
     * @return integer
     */
    public function countBackorderableByStockable(StockableInterface $stockable);
}
