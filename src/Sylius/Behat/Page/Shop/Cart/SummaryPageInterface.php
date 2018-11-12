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

use Sylius\Behat\Page\PageInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface SummaryPageInterface extends PageInterface
{
    public function getGrandTotal(): string;

    public function getBaseGrandTotal(): string;

    public function getTaxTotal(): string;

    public function getShippingTotal(): string;

    public function getPromotionTotal(): string;

    public function getItemTotal(string $productName): string;

    public function getItemUnitRegularPrice(string $productName): string;

    public function getItemUnitPrice(string $productName): string;

    public function isItemDiscounted(string $productName): bool;

    public function removeProduct(string $productName);

    public function changeQuantity(string $productName, int $quantity);

    public function applyCoupon(string $couponCode);

    public function isSingleItemOnPage(): bool;

    public function hasItemNamed(string $name): bool;

    public function hasItemWithCode(string $code): bool;

    public function hasItemWithVariantNamed(string $variantName): bool;

    public function hasItemWithOptionValue(string $productName, string $optionName, string $optionValue): string;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function isEmpty(): bool;

    public function getQuantity(string $productName): int;

    public function getCartTotal(): string;

    public function clearCart();

    public function updateCart();

    public function waitForRedirect(int $timeout);

    public function getPromotionCouponValidationMessage(): string;
}
