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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonsAwareInterface;

/**
 * Product interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductInterface extends
    BaseProductInterface,
    TaxableInterface,
    TaxonsAwareInterface,
    ChannelsAwareInterface
{
    /*
     * Variant selection methods.
     *
     * 1) Choice - A list of all variants is displayed to user.
     *
     * 2) Match  - Each product option is displayed as select field.
     *             User selects the values and we match them to variant.
     */
    const VARIANT_SELECTION_CHOICE = 'choice';
    const VARIANT_SELECTION_MATCH  = 'match';

    /**
     * Get product SKU.
     *
     * @return string
     */
    public function getSku();

    /**
     * Set product SKU.
     *
     * @param string $sku
     */
    public function setSku($sku);

    /**
     * Get the variant selection method.
     *
     * @return string
     */
    public function getVariantSelectionMethod();

    /**
     * Set variant selection method.
     *
     * @param string $variantSelectionMethod
     */
    public function setVariantSelectionMethod($variantSelectionMethod);

    /**
     * Check if variant is selectable by simple variant choice.
     *
     * @return Boolean
     */
    public function isVariantSelectionMethodChoice();

    /**
     * Get pretty label for variant selection method.
     *
     * @return string
     */
    public function getVariantSelectionMethodLabel();

    /**
     * Get product short description.
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Set product short description.
     *
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);

    /**
     * Set taxation category.
     *
     * @param TaxCategoryInterface $category
     */
    public function setTaxCategory(TaxCategoryInterface $category = null);

    /**
     * Get product shipping category.
     *
     * @return ShippingCategoryInterface
     */
    public function getShippingCategory();

    /**
     * Set product shipping category.
     *
     * @param ShippingCategoryInterface $category
     */
    public function setShippingCategory(ShippingCategoryInterface $category = null);

    /**
     * Get address zone restriction.
     *
     * @return ZoneInterface
     */
    public function getRestrictedZone();

    /**
     * Set address zone restriction.
     *
     * @param ZoneInterface $zone
     */
    public function setRestrictedZone(ZoneInterface $zone = null);

    /**
     * Get all product images.
     *
     * @return Collection|ImageInterface[]
     */
    public function getImages();

    /**
     * Get product main image.
     *
     * @return ImageInterface
     */
    public function getImage();
}
