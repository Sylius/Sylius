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
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return 'Shirt model number 485 Green L';
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableOnDemand()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold()
    {
        return 15;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold($onHold)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        return 50;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
    }
}
