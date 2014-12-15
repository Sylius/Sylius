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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\InStockInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Product\Model\VariantInterface as BaseVariantInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

/**
 * Sylius core product Variant interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantInterface extends BaseVariantInterface, ShippableInterface, StockableInterface, PriceableInterface
{
    /**
     * Get images.
     *
     * @return Collection|ProductVariantImageInterface[]
     */
    public function getImages();

    /**
     * Checks if product has image.
     *
     * @param ProductVariantImageInterface $image
     *
     * @return bool
     */
    public function hasImage(ProductVariantImageInterface $image);

    /**
     * Add image.
     *
     * @param ProductVariantImageInterface $image
     */
    public function addImage(ProductVariantImageInterface $image);

    /**
     * Remove image.
     *
     * @param ProductVariantImageInterface $image
     */
    public function removeImage(ProductVariantImageInterface $image);

    /**
     * @return int
     */
    public function getSold();

    /**
     * @param int $sold
     *
     * @return $this
     */
    public function setSold($sold);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $weight
     */
    public function setWeight($weight);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @param float $width
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @param float $height
     */
    public function setHeight($height);

    /**
     * @return float
     */
    public function getDepth();

    /**
     * @param float $depth
     */
    public function setDepth($depth);

    /**
     * Get stock keeping unit.
     *
     * @return mixed
     */
    public function getSku();

    /**
     * Set product SKU.
     *
     * @param string $sku
     *
     * @return ProductVariantInterface
     */
    public function setSku($sku);

    /**
     * Get inventory displayed name.
     *
     * @return string
     */
    public function getInventoryName();

    /**
     * Get all stockItems
     *
     * @return StockItemInterface[]|Collection
     */
    public function getStockItems();

    /**
     * Add a stockItem
     *
     * @param StockItemInterface $stockItem
     *
     * @return $this
     */
    public function addStockItem(StockItemInterface $stockItem);

    /**
     * Remove a  stockItem
     *
     * @param StockItemInterface $stockItem
     *
     * @return $this
     */
    public function removeStockItem(StockItemInterface $stockItem);

    /**
     * @param StockLocationInterface $location
     *
     * @return StockItemInterface
     */
    public function getStockItemForLocation(StockLocationInterface $location);

    public function getPrice();

    /**
     * {@inheritdoc}
     */
    public function setPrice($price);
}
