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

namespace Sylius\Behat\Page\Shop\Cart;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface SummaryPageInterface extends PageInterface
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

    public function getItemUnitRegularPrice(string $productName): int;

    public function getItemUnitPrice(string $productName): int;

    public function getItemImage(int $itemNumber): string;

    public function isItemDiscounted(string $productName): bool;

    public function removeProduct(string $productName): void;

    public function changeQuantity(string $productName, string $quantity): void;

    public function applyCoupon(string $couponCode): void;

    public function isSingleItemOnPage(): bool;

    public function hasItemNamed(string $name): bool;

    public function hasItemWithCode(string $code): bool;

    public function hasItemWithVariantNamed(string $variantName): bool;

    public function hasItemWithOptionValue(string $productName, string $optionName, string $optionValue): bool;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function isEmpty(): bool;

    public function getQuantity(string $productName): int;

    public function getCartTotal(): string;

    public function clearCart(): void;

    public function updateCart(): void;

    public function waitForRedirect(int $timeout): void;

    public function getPromotionCouponValidationMessage(): string;
}
