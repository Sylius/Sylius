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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UpdateSimpleProductPageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @return bool
     */
    public function isSlugReadOnlyIn($locale);
    
    /**
     * @param int $price
     */
    public function specifyPrice($price);

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);

    /**
     * @param string $attribute
     *
     * @return string
     */
    public function getAttributeValue($attribute);

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute);

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
     * @param string $code
     *
     * @return bool
     */
    public function isImageWithCodeDisplayed($code);

    /**
     * @param string $path
     * @param string $code
     */
    public function attachImage($path, $code = null);

    /**
     * @param string $code
     * @param string $path
     */
    public function changeImageWithCode($code, $path);

    /**
     * @param string $code
     */
    public function removeImageWithCode($code);

    public function removeFirstImage();

    /**
     * @return int
     */
    public function countImages();

    /**
     * @return bool
     */
    public function isImageCodeDisabled();

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForImage();

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
}
