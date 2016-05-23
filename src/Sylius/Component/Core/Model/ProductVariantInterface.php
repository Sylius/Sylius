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
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Product\Model\VariantInterface as BaseVariantInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantInterface extends
    BaseVariantInterface,
    ShippableInterface,
    StockableInterface,
    PriceableInterface,
    MetadataSubjectInterface,
    TaxableInterface
{
    const METADATA_CLASS_IDENTIFIER = 'ProductVariant';

    /**
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
     * @param ProductVariantImageInterface $image
     *
     * @return bool
     */
    public function hasImage(ProductVariantImageInterface $image);

    /**
     * @param ProductVariantImageInterface $image
     */
    public function addImage(ProductVariantImageInterface $image);

    /**
     * @param ProductVariantImageInterface $image
     */
    public function removeImage(ProductVariantImageInterface $image);

    /**
     * @return int
     */
    public function getSold();

    /**
     * @param int $sold
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
     * @return int
     */
    public function getOriginalPrice();

    /**
     * @param int|null $originalPrice
     */
    public function setOriginalPrice($originalPrice);

    /**
     * @return bool
     */
    public function isPriceReduced();

    /**
     * @param TaxCategoryInterface $category
     */
    public function setTaxCategory(TaxCategoryInterface $category = null);
}
