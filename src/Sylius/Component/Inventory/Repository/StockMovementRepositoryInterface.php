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

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * This interface should be implemented by repository of stock items.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockMovementRepositoryInterface extends RepositoryInterface
{
    /**
     * Create paginator for given location.
     *
     * @param integer $locationId
     *
     * @return PagerfantaInterface
     */
    public function createByLocationPaginator($locationId);
}
