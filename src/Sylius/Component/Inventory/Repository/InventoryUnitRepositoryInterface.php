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

use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
interface InventoryUnitRepositoryInterface extends RepositoryInterface
{
    /**
     * @param StockableInterface $stockable
     * @param string $state
     * @param int|null $limit
     *
     * @return InventoryUnitInterface[]
     */
    public function findByStockableAndInventoryState(StockableInterface $stockable, $state, $limit = null);
}
