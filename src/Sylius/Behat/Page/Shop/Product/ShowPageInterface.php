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

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

interface ShowPageInterface extends PageInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function addToCart(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithQuantity(string $quantity): void;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithVariant(string $variant): void;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue): void;

    public function getAttributeByName(string $name): ?string;

    public function getAttributeListByName(string $name): array;

    public function getAttributes(): array;

    public function getAverageRating(): float;

    public function getCatalogPromotionName(): string;

    public function hasCatalogPromotionApplied(string $name): bool;

    public function getCatalogPromotionNames(): array;

    public function getCatalogPromotions(): array;

    public function getCurrentUrl(): string;

    public function getCurrentVariantName(): string;

    public function getName(): string;

    public function getPrice(): string;

    public function getOriginalPrice(): ?string;

    public function isOriginalPriceVisible(): bool;

    public function hasAddToCartButton(): bool;

    public function hasAssociation(string $productAssociationName): bool;

    public function hasProductInAssociation(string $productName, string $productAssociationName): bool;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function hasReviewTitled(string $title): bool;

    public function isOutOfStock(): bool;

    public function isMainImageDisplayed(): bool;

    public function countReviews(): int;

    public function selectOption(string $optionCode, string $optionValue): void;

    public function selectVariant(string $variantName): void;

    public function visit(string $url): void;

    public function getVariantsNames(): array;

    public function getOptionValues(string $optionName): array;

    public function getDescription(): string;

    public function hasBreadcrumbLink(string $taxonName): bool;
}
