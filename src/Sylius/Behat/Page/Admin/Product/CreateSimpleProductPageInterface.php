<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $channelName
     * @param int $price
     */
    public function specifyPrice(string $channelName, int $price): void;

    /**
     * @param string $channelName
     * @param int $originalPrice
     */
    public function specifyOriginalPrice(string $channelName, int $originalPrice): void;

    /**
     * @param string $name
     */
    public function choosePricingCalculator(string $name): void;

    /**
     * @param string $channelName
     */
    public function checkChannel(string $channelName): void;

    /**
     * @param int $price
     * @param CurrencyInterface $currency
     */
    public function specifyPriceForChannelAndCurrency(int $price, ChannelInterface $channel, CurrencyInterface $currency): void;

    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn(string $name, string $localeCode): void;

    /**
     * @param string $slug
     * @param string $locale
     */
    public function specifySlugIn(string $slug, string $locale): void;

    /**
     * @param string $attributeName
     * @param string $value
     * @param string $localeCode
     */
    public function addAttribute(string $attributeName, string $value, string $localeCode): void;

    /**
     * @param $attributeName
     * @param $localeCode
     *
     * @return string
     */
    public function getAttributeValidationErrors($attributeName, $localeCode): string;

    /**
     * @param string $attributeName
     * @param string $localeCode
     */
    public function removeAttribute(string $attributeName, string $localeCode): void;

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage(string $path, string $type = null): void;

    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void;

    /**
     * @param string $productName
     */
    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): void;

    /**
     * @param string $locale
     */
    public function activateLanguageTab(string $locale): void;

    /**
     * @param string $shippingCategoryName
     */
    public function selectShippingCategory(string $shippingCategoryName): void;

    /**
     * @param bool $isShippingRequired
     */
    public function setShippingRequired(bool $isShippingRequired): void;
}
