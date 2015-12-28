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
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Product\Model\VariantInterface as BaseVariantInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

/**
 * Sylius core product Variant interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantInterface extends
    BaseVariantInterface,
    ShippableInterface,
    StockableInterface,
    PriceableInterface
{
    /**
     * Get images.
     *
     * @return Collection|ProductVariantImageInterface[]
     */
    public function getImages();

    /**
     * Get variant main image if any.
     * Fall-back on product master variant
     *
     * @return ImageInterface
     */
    public function getImage();

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
     * Get original price.
     *
     * @return integer
     */
    public function getOriginalPrice();

    /**
     * Set original price.
     *
     * @param integer $originalPrice
     */
    public function setOriginalPrice($originalPrice);

    /**
     * @return bool
     */
    public function isPriceReduced();
}
