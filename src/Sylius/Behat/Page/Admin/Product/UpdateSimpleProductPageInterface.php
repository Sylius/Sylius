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

    public function specifyPrice(string $channelName, int $price);

    public function specifyOriginalPrice(string $channelName, int $originalPrice);

    public function nameItIn(string $name, string $localeCode);

    public function addSelectedAttributes();

    public function removeAttribute(string $attributeName, string $localeCode);

    public function getAttributeValue(string $attributeName, string $localeCode): string;

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    public function getNumberOfAttributes(): int;

    public function hasAttribute(string $attributeName): bool;

    public function isMainTaxonChosen(string $taxonName): bool;

    public function selectMainTaxon(TaxonInterface $taxon);

    public function disableTracking();

    public function enableTracking();

    public function isTracked(): bool;

    public function enableSlugModification(string $locale);

    public function isImageWithTypeDisplayed(string $type): bool;

    /**
     * @param string $type
     */
    public function attachImage(string $path, string $type = null);

    public function changeImageWithType(string $type, string $path);

    public function removeImageWithType(string $type);

    public function removeFirstImage();

    public function modifyFirstImageType(string $type);

    public function countImages(): int;

    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames);

    public function hasAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): bool;

    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType);

    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string;

    public function activateLanguageTab(string $locale);

    public function getSlug(string $locale): string;

    public function specifySlugIn(string $slug, string $locale);

    public function getPriceForChannel(string $channelName): string;

    public function getOriginalPriceForChannel(string $channelName): string;

    public function isShippingRequired(): bool;
}
