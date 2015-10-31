<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Inventory\Model\StockLocationInterface as BaseStockLocationInterface;

/**
 * Stock location with an address.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockLocationInterface extends BaseStockLocationInterface
{
    /**
     * Get the address of the location.
     *
     * @return null|AddressInterface
     */
    public function getAddress();

    /**
     * Define the address of location.
     *
     * @param AddressInterface $address
     */
    public function setAddress(AddressInterface $address = null);
}
