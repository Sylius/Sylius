<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

/**
 * Interface for objects that reference stock location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockLocationAwareInterface
{
    public function getStockLocation();
    public function setStockLocation(StockLocationInterface $location = null);
}
