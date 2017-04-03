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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
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
     */
    public function choosePricingCalculator($name);

    /**
     * @param string $channelName
     */
    public function checkChannel($channelName);

    /**
     * @param int $price
     * @param ChannelInterface $channel
     * @param CurrencyInterface $currency
     */
    public function specifyPriceForChannelAndCurrency($price, ChannelInterface $channel, CurrencyInterface $currency);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);

    /**
     * @param string $slug
     * @param string $locale
     */
    public function specifySlugIn($slug, $locale);

    /**
     * @param string $attributeName
     * @param string $value
     * @param string $localeCode
     */
    public function addAttribute($attributeName, $value, $localeCode);

    /**
     * @param $attributeName
     * @param $localeCode
     *
     * @return string
     */
    public function getAttributeValidationErrors($attributeName, $localeCode);

    /**
     * @param string $attributeName
     * @param string $localeCode
     */
    public function removeAttribute($attributeName, $localeCode);

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage($path, $type = null);

    /**
     * @param ProductAssociationTypeInterface $productAssociationType
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames);

    /**
     * @param string $productName
     * @param ProductAssociationTypeInterface $productAssociationType
     */
    public function removeAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType);

    /**
     * @param string $locale
     */
    public function activateLanguageTab($locale);

    /**
     * @param string $shippingCategoryName
     */
    public function selectShippingCategory($shippingCategoryName);

    /**
     * @param bool $isShippingRequired
     */
    public function setShippingRequired($isShippingRequired);
}
