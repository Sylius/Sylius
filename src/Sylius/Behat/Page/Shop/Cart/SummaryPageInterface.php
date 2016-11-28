<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Cart;

use Sylius\Behat\Page\PageInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
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

    /**
     * @return string
     */
    public function getTaxTotal();

    /**
     * @return string
     */
    public function getShippingTotal();

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
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @param $productName
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
