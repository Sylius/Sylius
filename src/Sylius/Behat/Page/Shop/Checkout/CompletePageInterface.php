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

namespace Sylius\Behat\Page\Shop\Checkout;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

interface CompletePageInterface extends SymfonyPageInterface
{
    /**
     * @param string $productName
     * @param string $quantity
     *
     * @return bool
     */
    public function hasItemWithProductAndQuantity($productName, $quantity);

    /**
     * @return bool
     */
    public function hasShippingAddress(AddressInterface $address);

    /**
     * @return bool
     */
    public function hasBillingAddress(AddressInterface $address);

    /**
     * @return bool
     */
    public function getPaymentMethodName();

    /**
     * @return bool
     */
    public function hasPaymentMethod();

    /**
     * @return bool
     */
    public function hasShippingMethod(ShippingMethodInterface $shippingMethod);

    /**
     * @param float $amount
     *
     * @return bool
     */
    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, $amount);

    /**
     * @param float $total
     *
     * @return bool
     */
    public function hasOrderTotal($total);

    /**
     * @param string $notes
     */
    public function addNotes($notes);

    /**
     * @param string $promotionTotal
     *
     * @return bool
     */
    public function hasPromotionTotal($promotionTotal);

    /**
     * @param string $promotionName
     *
     * @return bool
     */
    public function hasPromotion($promotionName);

    public function hasShippingPromotion(string $promotionName): bool;

    public function getTaxTotal(): string;

    /**
     * @param string $price
     *
     * @return bool
     */
    public function hasShippingTotal($price);

    /**
     * @param string $price
     *
     * @return bool
     */
    public function hasProductUnitPrice(ProductInterface $product, $price);

    /**
     * @param string $localeName
     *
     * @return bool
     */
    public function hasLocale($localeName);

    /**
     * @param string $currencyCode
     *
     * @return bool
     */
    public function hasCurrency($currencyCode);

    /**
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product);

    public function changeAddress();

    public function changeShippingMethod();

    public function changePaymentMethod();

    public function confirmOrder();

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasShippingProvinceName($provinceName);

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasBillingProvinceName($provinceName);

    /**
     * @return string
     */
    public function getBaseCurrencyOrderTotal();

    /**
     * @param string $promotionName
     *
     * @return string
     */
    public function getShippingPromotionDiscount($promotionName);

    /**
     * @return string
     */
    public function getValidationErrors();

    public function hasShippingPromotionWithDiscount(string $promotionName, string $discount): bool;

    public function hasOrderPromotion(string $promotionName): bool;
}
