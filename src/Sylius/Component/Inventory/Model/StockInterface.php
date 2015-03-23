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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\VariantInterface as BaseVariantInterface;

/**
 * Sylius core stock interface.
 *
 * @author Myke Hines <myke@webhines.com>
 */
interface StockInterface 
{
    /**
     * @return boolean
     */
    public function isManageStock();

    /** 
     * @param boolean $manage_stock
     */
    public function setManageStock($manage_stock);

    /**
     * @return float
     */
    public function getOnHand();

    /**
     * @param float $on_hand
     */
    public function setOnHand($on_hand);

    /**
     * @return float
     */
    public function getOnHold();

    /**
     * @param float $on_hold
     */
    public function setOnHold($on_hold);

    /**
     * @return boolean
     */
    public function hasAllowBackorders();

    /**
     * @param boolean $allow_backorders
     */
    public function setAllowBackorders($allow_backorders);

    /**
     * @return float
     */
    public function getMinQuantityInCart();

    /**
     * @param float $min_quantity_in_cart
     */
    public function setMinQuantityInCart($min_quantity_in_cart);

    /**
     * @return float
     */
    public function getMaxQuantityInCart();

    /**
     * @param float $max_quantity_in_cart
     */
    public function setMaxQuantityInCart($max_quantity_in_cart);

    /**
     * @return float
     */
    public function getMinStockLevel();

    /**
     * @param float $min_stock_level
     */
    public function setMinStockLevel($min_stock_level);

}
