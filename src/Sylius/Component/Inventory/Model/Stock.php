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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Pricing\Calculators;
use Sylius\Component\Product\Model\Variant as BaseVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * Sylius core inventory manager entity.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class Stock implements StockInterface
{
    /**
     * id.
     *
     * @var mixed
     */
    protected $id;

    /** 
     * Should we manage anything about stock?
     *
     * @var boolean
     */
    protected $manageStock = false;

    /**
     * On hand stock.
     *
     * @var int
     */
    protected $onHand = 0;

    /**
     * On hold stock.
     *
     * @var int
     */
    protected $onHold = 0;

    /**
     * Is variant available on backorders?
     *
     * @var bool
     */
    protected $allowBackorders = true;

    /**
     * Minimum allowed in cart
     *
     * @var float
     */
    protected $minQuantityInCart = null;

    /**
     * Maximum allowed in cart
     *
     * @var float
     */
    protected $maxQuantityInCart = null;

    /**
     * Minimum before product is out of stock
     *
     * @var float
     */
    protected $minStockLevel = 0;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     * Required for forms
     */
    public function isManageStock()
    {
        return $this->manageStock;
    }

    /**
     * {@inheritdoc}
     */
    public function setManageStock($manageStock)
    {
        $this->manageStock = (bool)$manageStock;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        return $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
        $this->onHand = $onHand;

        if (0 > $this->onHand) {
            $this->onHand = 0;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold()
    {
        return $this->onHold;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAllowBackorders()
    {
        return $this->allowBackorders;
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowBackorders($allowBackorders)
    {
        $this->allowBackorders = (bool)$allowBackorders;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinQuantityInCart()
    {
        return $this->minQuantityInCart;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinQuantityInCart($minQuantityInCart)
    {
        $this->minQuantityInCart = $minQuantityInCart;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxQuantityInCart()
    {
        return $this->maxQuantityInCart;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxQuantityInCart($maxQuantityInCart)
    {
        $this->maxQuantityInCart = $maxQuantityInCart;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinStockLevel()
    {
        return $this->minStockLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinStockLevel($minStockLevel)
    {
        $this->minStockLevel = $minStockLevel;

        return $this;
    }    
}
