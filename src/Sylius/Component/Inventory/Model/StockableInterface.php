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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockableInterface
{
    /**
     * @return string
     */
    public function getInventoryName();

    /**
     * @return bool
     */
    public function isInStock();

    /**
     * @return int
     */
    public function getOnHold();

    /**
     * @param int $onHold
     */
    public function setOnHold($onHold);

    /**
     * @return int
     */
    public function getOnHand();

    /**
     * @param int $onHand
     */
    public function setOnHand($onHand);

    /**
     * @return bool
     */
    public function isTracked();

    /**
     * @param bool $tracked
     */
    public function setTracked($tracked);
}
