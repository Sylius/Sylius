<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface UpdateSimpleProductPageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $locale
     *
     * @return bool
     */
    public function isSlugReadonlyIn($locale);

    /**
     * @param string $channelName
     * @param int $price
     */
    public function specifyPrice($channelName, $price);

    /**
     * @param string $channelName
     * @param int $originalPrice
     */
    public function specifyOriginalPrice($channelName, $originalPrice);

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);

    public function addSelectedAttributes();

    /**
     * @param string $attributeName
     * @param string $localeCode
     */
    public function removeAttribute($attributeName, $localeCode);

    /**
     * @param string $attributeName
     * @param string $localeCode
     *
     * @return string
     */
    public function getAttributeValue($attributeName, $localeCode);

    /**
     * @param string $attributeName
     * @param string $localeCode
     *
     * @return string
     */
    public function getAttributeValidationErrors($attributeName, $localeCode);

    /**
     * @return int
     */
    public function getNumberOfAttributes();

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function hasAttribute($attributeName);

    /**
     * @param string $taxonName
     *
     * @return bool
     */
    public function isMainTaxonChosen($taxonName);

    /**
     * @param TaxonInterface $taxon
     */
    public function selectMainTaxon(TaxonInterface $taxon);

    public function disableTracking();

    public function enableTracking();

    /**
     * @return bool
     */
    public function isTracked();

    /**
     * @param string $locale
     */
    public function enableSlugModification($locale);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isImageWithTypeDisplayed($type);

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage($path, $type = null);

    /**
     * @param string $type
     * @param string $path
     */
    public function changeImageWithType($type, $path);

    /**
     * @param string $type
     */
    public function removeImageWithType($type);

    public function removeFirstImage();

    /**
     * @param string $type
     */
    public function modifyFirstImageType($type);

    /**
     * @return int
     */
    public function countImages();

    /**
     * @param ProductAssociationTypeInterface $productAssociationType
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames);

    /**
     * @param string $productName
     * @param ProductAssociationTypeInterface $productAssociationType
     *
     * @return bool
     */
    public function hasAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType);

    /**
     * @param string $productName
     * @param ProductAssociationTypeInterface $productAssociationType
     */
    public function removeAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType);

    /**
     * @param ChannelInterface $channel
     * @param CurrencyInterface $currency
     *
     * @return string
     */
    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency);

    /**
     * @param string $locale
     */
    public function activateLanguageTab($locale);

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getSlug($locale);

    /**
     * @param string $slug
     * @param string $locale
     */
    public function specifySlugIn($slug, $locale);

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getPriceForChannel($channelName);

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getOriginalPriceForChannel($channelName);

    /**
     * @return bool
     */
    public function isShippingRequired();
}
