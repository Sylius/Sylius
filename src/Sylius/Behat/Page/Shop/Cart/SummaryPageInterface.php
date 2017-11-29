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
    /**
     * @return string
     */
    public function getGrandTotal(): string;

    /**
     * @return string
     */
    public function getBaseGrandTotal(): string;

    /**
     * @return string
     */
    public function getTaxTotal(): string;

    /**
     * @return string
     */
    public function getShippingTotal(): string;

    /**
     * @return string
     */
    public function getPromotionTotal(): string;

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getItemTotal(string $productName): string;

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getItemUnitRegularPrice(string $productName): string;

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getItemUnitPrice(string $productName): string;

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isItemDiscounted(string $productName): bool;

    /**
     * @param string $productName
     */
    public function removeProduct(string $productName): void;

    /**
     * @param string $productName
     * @param int $quantity
     */
    public function changeQuantity(string $productName, int $quantity): void;

    /**
     * @param string $couponCode
     */
    public function applyCoupon(string $couponCode): void;

    /**
     * @return bool
     */
    public function isSingleItemOnPage(): bool;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasItemNamed(string $name): bool;

    /**
     * @param string $code
     *
     * @return bool
     */
    public function hasItemWithCode(string $code): bool;

    /**
     * @param string $variantName
     *
     * @return bool
     */
    public function hasItemWithVariantNamed(string $variantName): bool;

    /**
     * @param string $productName
     * @param string $optionName
     * @param string $optionValue
     *
     * @return string
     */
    public function hasItemWithOptionValue(string $productName, string $optionName, string $optionValue): string;

    /**
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param $productName
     *
     * @return int
     */
    public function getQuantity($productName): int;

    /**
     * @return string
     */
    public function getCartTotal(): string;

    public function clearCart(): void;

    public function updateCart(): void;

    /**
     * @param int $timeout
     */
    public function waitForRedirect(int $timeout): void;

    /**
     * @return string
     */
    public function getPromotionCouponValidationMessage(): string;
}
