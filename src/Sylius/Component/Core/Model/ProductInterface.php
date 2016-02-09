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
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonsAwareInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface ProductInterface extends
    BaseProductInterface,
    TaxonsAwareInterface,
    ChannelsAwareInterface,
    MetadataSubjectInterface
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
    const VARIANT_SELECTION_MATCH = 'match';

    const METADATA_CLASS_IDENTIFIER = 'Product';

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
    public function getVariantSelectionMethod();

    /**
     * @param string $variantSelectionMethod
     */
    public function setVariantSelectionMethod($variantSelectionMethod);

    /**
     * @return bool
     */
    public function isVariantSelectionMethodChoice();

    /**
     * @return string
     */
    public function getVariantSelectionMethodLabel();

    /**
     * @return string
     */
    public function getShortDescription();

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);

    /**
     * @return ShippingCategoryInterface
     */
    public function getShippingCategory();

    /**
     * @param ShippingCategoryInterface $category
     */
    public function setShippingCategory(ShippingCategoryInterface $category = null);

    /**
     * Get master variant price.
     *
     * @return int
     */
    public function getPrice();

    /**
     * Set master variant price.
     *
     * @param int $price
     */
    public function setPrice($price);

    /**
     * Get address zone restriction.
     *
     * @return ZoneInterface
     */
    public function getRestrictedZone();

    /**
     * @param ZoneInterface $zone
     */
    public function setRestrictedZone(ZoneInterface $zone = null);

    /**
     * @return Collection|ImageInterface[]
     */
    public function getImages();

    /**
     * @return ImageInterface
     */
    public function getImage();

    /**
     * @return TaxonInterface
     */
    public function getMainTaxon();

    /**
     * @param TaxonInterface $mainTaxon
     */
    public function setMainTaxon(TaxonInterface $mainTaxon = null);
}
