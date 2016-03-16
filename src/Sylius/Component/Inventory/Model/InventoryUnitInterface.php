<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface InventoryUnitInterface extends TimestampableInterface
{
    /**
     * Default states.
     */
    const STATE_CHECKOUT = 'checkout';
    const STATE_ONHOLD = 'onhold';
    const STATE_SOLD = 'sold';
    const STATE_BACKORDERED = 'backordered';
    const STATE_RETURNED = 'returned';

    /**
     * @return StockableInterface
     */
    public function getStockable();

    /**
     * @return string
     */
    public function getInventoryName();

    /**
     * @return string
     */
    public function getInventoryState();

    /**
     * @param string $state
     */
    public function setInventoryState($state);

    /**
     * @return bool
     */
    public function isSold();

    /**
     * @return bool
     */
    public function isBackordered();
}
