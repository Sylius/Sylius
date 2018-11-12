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
    public function addToCart();

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithQuantity(string $quantity);

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithVariant(string $variant);

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue);

    public function getName(): string;

    public function getCurrentVariantName(): string;

    public function visit(string $url);

    public function getAttributeByName(string $name): ?string;

    public function getAttributes(): array;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function waitForValidationErrors(int $timeout);

    public function isOutOfStock(): bool;

    public function hasAddToCartButton(): bool;

    public function getPrice(): string;

    public function countReviews(): int;

    public function isMainImageDisplayed(): bool;

    public function hasReviewTitled(string $title): bool;

    public function getAverageRating(): float;

    public function selectOption(string $optionName, string $optionValue);

    public function selectVariant(string $variantName);

    public function hasAssociation(string $productAssociationName): bool;

    public function hasProductInAssociation(string $productName, string $productAssociationName): bool;
}
