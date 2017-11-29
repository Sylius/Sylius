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

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

interface ShowPageInterface extends PageInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function addToCart(): void;

    /**
     * @param string $quantity
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithQuantity(string $quantity): void;

    /**
     * @param string $variant
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithVariant(string $variant): void;

    /**
     * @param string $optionValue
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getCurrentVariantName(): string;

    /**
     * @param string $url
     */
    public function visit(string $url): void;

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getAttributeByName(string $name): ?string;

    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    /**
     * @param int $timeout
     */
    public function waitForValidationErrors(int $timeout): void;

    /**
     * @return bool
     */
    public function isOutOfStock(): bool;

    /**
     * @return bool
     */
    public function hasAddToCartButton(): bool;

    /**
     * @return string
     */
    public function getPrice(): string;

    /**
     * @return int
     */
    public function countReviews(): int;

    /**
     * @return bool
     */
    public function isMainImageDisplayed(): bool;

    /**
     * @param string $title
     *
     * @return bool
     */
    public function hasReviewTitled(string $title): bool;

    /**
     * @return float
     */
    public function getAverageRating(): float;

    /**
     * @param string $optionName
     * @param string $optionValue
     */
    public function selectOption(string $optionName, string $optionValue): void;

    /**
     * @param string $variantName
     */
    public function selectVariant(string $variantName): void;

    /**
     * @param string $productAssociationName
     *
     * @return bool
     */
    public function hasAssociation(string $productAssociationName): bool;

    /**
     * @param string $productName
     * @param string $productAssociationName
     *
     * @return bool
     */
    public function hasProductInAssociation(string $productName, string $productAssociationName): bool;
}
