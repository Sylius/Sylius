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
    /**
     * @return string
     */
    public function getGrandTotal();

    /**
     * @return string
     */
    public function getBaseGrandTotal();

    public function getIncludedTaxTotal(): string;

    public function getExcludedTaxTotal(): string;

    public function areTaxesCharged(): bool;

    /**
     * @return string
     */
    public function getShippingTotal();

    public function hasShippingTotal(): bool;

    /**
     * @return string
     */
    public function getPromotionTotal();

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getItemTotal($productName);

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getItemUnitRegularPrice($productName);

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getItemUnitPrice($productName);

    public function getItemImage(int $itemNumber): string;

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isItemDiscounted($productName);

    /**
     * @param string $productName
     */
    public function removeProduct($productName);

    /**
     * @param string $productName
     * @param int $quantity
     */
    public function changeQuantity($productName, $quantity);

    /**
     * @param string $couponCode
     */
    public function applyCoupon($couponCode);

    /**
     * @return bool
     */
    public function isSingleItemOnPage();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasItemNamed($name);

    /**
     * @param string $code
     *
     * @return bool
     */
    public function hasItemWithCode($code);

    /**
     * @param string $variantName
     *
     * @return bool
     */
    public function hasItemWithVariantNamed($variantName);

    /**
     * @param string $productName
     * @param string $optionName
     * @param string $optionValue
     *
     * @return string
     */
    public function hasItemWithOptionValue($productName, $optionName, $optionValue);

    /**
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @param string $productName
     *
     * @return int
     */
    public function getQuantity($productName);

    /**
     * @return string
     */
    public function getCartTotal();

    public function clearCart();

    public function updateCart();

    /**
     * @param int $timeout
     */
    public function waitForRedirect($timeout);

    /**
     * @return string
     */
    public function getPromotionCouponValidationMessage();
}
