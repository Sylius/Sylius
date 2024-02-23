<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface UpdateConfigurableProductPageInterface extends UpdatePageInterface
{
    public function isCodeDisabled(): bool;

    public function nameItIn(string $name, string $localeCode): void;

    public function setMetaKeywords(string $keywords, string $localeCode): void;

    public function setMetaDescription(string $description, string $localeCode): void;

    public function isProductOptionChosen(string $option): bool;

    public function isProductOptionsDisabled(): bool;

    public function hasMainTaxonWithName(string $taxonName): bool;

    public function selectMainTaxon(TaxonInterface $taxon): void;

    public function checkChannel(string $channelName): void;

    public function isImageWithTypeDisplayed(string $type): bool;

    public function hasLastImageAVariant(ProductVariantInterface $productVariant): bool;

    public function attachImage(string $path, ?string $type = null, ?ProductVariantInterface $productVariant = null): void;

    public function changeImageWithType(string $type, string $path): void;

    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    public function modifyFirstImageType(string $type): void;

    public function selectVariantForFirstImage(ProductVariantInterface $productVariant): void;

    public function countImages(): int;

    public function goToVariantsList(): void;

    public function goToVariantCreation(): void;

    public function goToVariantGeneration(): void;

    public function hasInventoryTab(): bool;
}
