<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Fixtures;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SampleStockable implements StockableInterface
{
    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return 'SHIRT-485-LARGE-GREEN';
    }

    /**
     * @inheritDoc
     */
    public function getInventoryName()
    {
        return 'Shirt model number 485 Green L';
    }

    /**
     * @inheritDoc
     */
    public function isInStock()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isAvailableOnDemand()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getOnHold()
    {
        return 15;
    }

    /**
     * @inheritDoc
     */
    public function setOnHold($onHold)
    {
    }

    /**
     * @inheritDoc
     */
    public function getOnHand()
    {
        return 50;
    }

    /**
     * @inheritDoc
     */
    public function setOnHand($onHand)
    {
    }
}
