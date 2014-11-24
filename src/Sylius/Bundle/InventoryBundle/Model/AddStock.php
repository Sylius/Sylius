<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sylius\Bundle\InventoryBundle\Model;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;

class AddStock
{

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var ProductVariantInterface
     */
    protected $productVariant;

    /**
     * @var StockLocationInterface
     */
    protected $stockLocation;

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * @param ProductVariantInterface $productVariant
     */
    public function setProductVariant(ProductVariantInterface $productVariant)
    {
        $this->productVariant = $productVariant;
    }

    /**
     * @return StockLocationInterface
     */
    public function getStockLocation()
    {
        return $this->stockLocation;
    }

    /**
     * @param StockLocationInterface $stockLocation
     */
    public function setStockLocation(StockLocationInterface $stockLocation)
    {
        $this->stockLocation = $stockLocation;
    }
}