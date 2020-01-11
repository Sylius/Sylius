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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
    public function specifyPrice(string $channelName, string $price): void;

    public function specifyOriginalPrice(string $channelName, int $originalPrice): void;

    public function choosePricingCalculator(string $name): void;

    public function checkChannel(string $channelName): void;

    public function specifyCode(string $code): void;

    public function nameItIn(string $name, string $localeCode): void;

    public function specifySlugIn(?string $slug, string $locale): void;

    public function addAttribute(string $attributeName, string $value, string $localeCode): void;

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    public function removeAttribute(string $attributeName, string $localeCode): void;

    public function isMainTaxonChosen(string $taxonName): bool;

    public function selectMainTaxon(TaxonInterface $taxon): void;

    public function attachImage(string $path, ?string $type = null): void;

    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void;

    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): void;

    public function activateLanguageTab(string $locale): void;

    public function selectShippingCategory(string $shippingCategoryName): void;

    public function setShippingRequired(bool $isShippingRequired): void;
}
