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
interface StockableInterface extends StockItemsAwareInterface
{
    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     */
    public function setSku($sku);

    /**
     * @return string
     */
    public function getInventoryName();

    /**
     * Simply checks if there any stock available.
     * It should also return true for items available on demand.
     *
     * @return bool
     */
    public function isInStock();

    /**
     * @return bool
     */
    public function isAvailableOnDemand();

    /**
     * @param $bool
     */
    public function setAvailableOnDemand($bool);

    /**
     * @return int
     */
    public function getOnHold();

    /**
     * @param int
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
}
