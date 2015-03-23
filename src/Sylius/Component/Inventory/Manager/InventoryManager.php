<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Manager;

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

/**
 * Checks availability for given stockable object.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryManager implements InventoryManagerInterface
{
    /**
     * Are backorders enabled?
     *
     * @var bool
     */
    protected $backorders;

    /**
     * Constructor.
     *
     * @param bool $backorders
     */
    public function __construct($backorders)
    {
        $this->backorders = (bool) $backorders;
    }

    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable, $quantity = 1)
    {
        if ($stockable instanceof SoftDeletableInterface && $stockable->isDeleted()) {
            return false;
        }

        if ($this->backorders || $this->isInStock($stockable, $quantity)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isStockConvertable(StockableInterface $stockable, $quantity=1)
    {
        if ($stockable instanceof SoftDeletableInterface && $stockable->isDeleted()) {
            return false;
        }

        if (!$this->isStockAvailable($stockable, $quantity))
            return false;

        $stock = $this->getStock($stockable);

        if ($stock->isManageStock())
        {
            if (null !== $stock->getMinQuantityInCart() && $stock->getMinQuantityInCart() > $quantity)
                throw new MinimumInsufficientRequirementsException($stockable, $quantity, $stock->getMinQuantityInCart());
        
            if (null !== $stock->getMaxQuantityInCart() && $stock->getMaxQuantityInCart() < $quantity)
                throw new MaximumInsufficientRequirementsException($stockable, $quantity, $stock->getMaxQuantityInCart());
        }

        return true;
    }        

    /**
     * Checks the stock level against the requested quantity
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     *
     * @return Boolean
     */
    private function isInStock(StockableInterface $stockable, $quantity = 1)
    {
        $stock = $this->getStock($stockable);

        if (!$stock->isManageStock() || $stock->hasAllowBackorders())
            return true;

        if ($quantity <= $stock->getOnHand() - $stock->getOnHold() - $stock->getMinStockLevel())
            return true;

        return false;
    }    

    /**
     * Get's the stockable inventory values or the default value
     *
     * @param StockableInterface $stockable
     *
     * @return InventoryManager
     */
    private function getStock(StockableInterface $stockable)
    {
        return $stockable->getStock();
    }
}
