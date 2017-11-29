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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

interface UpdateSimpleProductPageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $locale
     *
     * @return bool
     */
    public function isSlugReadonlyIn(string $locale): bool;

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
     * @param string $localeCode
     */
    public function nameItIn(string $name, string $localeCode): void;

    public function addSelectedAttributes(): void;

    /**
     * @param string $attributeName
     * @param string $localeCode
     */
    public function removeAttribute(string $attributeName, string $localeCode): void;

    /**
     * @param string $attributeName
     * @param string $localeCode
     *
     * @return string
     */
    public function getAttributeValue(string $attributeName, string $localeCode): string;

    /**
     * @param string $attributeName
     * @param string $localeCode
     *
     * @return string
     */
    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    /**
     * @return int
     */
    public function getNumberOfAttributes(): int;

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function hasAttribute(string $attributeName): bool;

    /**
     * @param string $taxonName
     *
     * @return bool
     */
    public function isMainTaxonChosen(string $taxonName): bool;

    /**
     * @param TaxonInterface $taxon
     */
    public function selectMainTaxon(TaxonInterface $taxon): void;

    public function disableTracking(): void;

    public function enableTracking(): void;

    /**
     * @return bool
     */
    public function isTracked(): bool;

    /**
     * @param string $locale
     */
    public function enableSlugModification(string $locale): void;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isImageWithTypeDisplayed(string $type): bool;

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage(string $path, string $type = null): void;

    /**
     * @param string $type
     * @param string $path
     */
    public function changeImageWithType(string $type, string $path): void;

    /**
     * @param string $type
     */
    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    /**
     * @param string $type
     */
    public function modifyFirstImageType(string $type): void;

    /**
     * @return int
     */
    public function countImages(): int;

    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void;

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function hasAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): bool;

    /**
     * @param string $productName
     */
    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): void;

    /**
     * @param CurrencyInterface $currency
     *
     * @return string
     */
    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string;

    /**
     * @param string $locale
     */
    public function activateLanguageTab(string $locale): void;

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getSlug(string $locale): string;

    /**
     * @param string $slug
     * @param string $locale
     */
    public function specifySlugIn(string $slug, string $locale): void;

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getPriceForChannel(string $channelName): string;

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getOriginalPriceForChannel(string $channelName): string;

    /**
     * @return bool
     */
    public function isShippingRequired(): bool;
}
