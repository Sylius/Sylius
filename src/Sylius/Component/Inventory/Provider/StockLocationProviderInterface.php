<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Provider;

use Sylius\Component\Inventory\Model\InventorySubjectInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;

/**
 * This service returns all the available stock locations for given subject.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockLocationProviderInterface
{
    /**
     * Get all available stock locations.
     *
     * @param InventorySubjectInterface
     *
     * @return StockLocationInterface[]
     */
    public function getAvailableStockLocations(InventorySubjectInterface $subject);
}
