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

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
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
    ReviewableInterface
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
     * @return TaxonInterface
     */
    public function getMainTaxon();

    /**
     * @param TaxonInterface $mainTaxon
     */
    public function setMainTaxon(TaxonInterface $mainTaxon = null);

    /**
     * @return ProductVariantInterface
     */
    public function getFirstVariant();

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @return ImageInterface
     */
    public function getImage();

    /**
     * @return ImageInterface[]
     */
    public function getImages();
}
