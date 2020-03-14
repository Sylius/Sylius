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
    public function isCodeDisabled(): bool;

    public function isSlugReadonlyIn(string $locale): bool;

    public function specifyPrice(string $channelName, string $price): void;

    public function specifyOriginalPrice(string $channelName, string $originalPrice): void;

    public function nameItIn(string $name, string $localeCode): void;

    public function addSelectedAttributes(): void;

    public function removeAttribute(string $attributeName, string $localeCode): void;

    public function getAttributeValue(string $attributeName, string $localeCode): string;

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    public function getNumberOfAttributes(): int;

    public function hasAttribute(string $attributeName): bool;

    public function isMainTaxonChosen(string $taxonName): bool;

    public function selectMainTaxon(TaxonInterface $taxon): void;

    public function disableTracking(): void;

    public function enableTracking(): void;

    public function isTracked(): bool;

    public function enableSlugModification(string $locale): void;

    public function isImageWithTypeDisplayed(string $type): bool;

    /**
     * @param string $type
     */
    public function attachImage(string $path, string $type = null): void;

    public function changeImageWithType(string $type, string $path): void;

    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    public function modifyFirstImageType(string $type): void;

    public function countImages(): int;

    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void;

    public function hasAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): bool;

    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): void;

    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string;

    public function activateLanguageTab(string $locale): void;

    public function getSlug(string $locale): string;

    public function specifySlugIn(string $slug, string $locale): void;

    public function getPriceForChannel(string $channelName): string;

    public function getOriginalPriceForChannel(string $channelName): string;

    public function isShippingRequired(): bool;

    public function goToVariantsList(): void;

    public function goToVariantCreation(): void;

    public function goToVariantGeneration(): void;

    public function hasInventoryTab(): bool;

    public function isShowInShopButtonDisabled(): bool;

    public function showProductInChannel(string $channel): void;

    public function showProductInSingleChannel(): void;

    public function disable(): void;

    public function isEnabled(): bool;

    public function enable(): void;
}
