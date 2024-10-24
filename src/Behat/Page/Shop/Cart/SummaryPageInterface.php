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

namespace Sylius\Behat\Page\Shop\Cart;

use Sylius\Behat\Page\Shop\PageInterface as ShopPageInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface SummaryPageInterface extends ShopPageInterface
{
    public function getGrandTotal(): string;

    public function getBaseGrandTotal(): string;

    public function getIncludedTaxTotal(): string;

    public function getExcludedTaxTotal(): string;

    public function areTaxesCharged(): bool;

    public function getShippingTotal(): string;

    public function hasShippingTotal(): bool;

    public function getPromotionTotal(): string;

    public function getItemsTotal(): string;

    public function getItemTotal(string $productName): string;

    public function getItemUnitRegularPrice(string $productName): string;

    public function getItemUnitPrice(string $productName): string;

    public function hasOriginalPrice(string $productName): bool;

    public function getItemImage(int $itemNumber): string;

    public function isItemDiscounted(string $productName): bool;

    public function removeProduct(string $productName): void;

    public function changeQuantity(string $productName, string $quantity): void;

    public function applyCoupon(string $couponCode): void;

    public function removeCoupon(): void;

    public function countOrderItems(): int;

    public function hasItemNamed(string $name): bool;

    public function hasItemWithCode(string $code): bool;

    public function hasItemWithVariantNamed(string $variantName): bool;

    public function getItemOptionValue(string $productName, string $optionName): string;

    public function hasItemWithInsufficientStock(string $productName): bool;

    public function cartIsEmpty(): bool;

    public function getQuantity(string $productName): int;

    public function getCartTotal(): string;

    public function clearCart(): void;

    public function checkout(): void;

    public function waitForRedirect(int $timeout): void;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;
}
