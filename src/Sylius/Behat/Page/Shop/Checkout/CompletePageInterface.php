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

use Sylius\Behat\Page\SymfonyPageInterface;
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
    public function hasItemWithProductAndQuantity(string $productName, string $quantity): bool;

    /**
     * @return bool
     */
    public function hasShippingAddress(AddressInterface $address): bool;

    /**
     * @return bool
     */
    public function hasBillingAddress(AddressInterface $address): bool;

    /**
     * @return bool
     */
    public function getPaymentMethodName(): bool;

    /**
     * @return bool
     */
    public function hasPaymentMethod(): bool;

    /**
     * @return bool
     */
    public function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool;

    /**
     * @param float $amount
     *
     * @return bool
     */
    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, float $amount): bool;

    /**
     * @param float $total
     *
     * @return bool
     */
    public function hasOrderTotal(float $total): bool;

    /**
     * @param string $notes
     */
    public function addNotes(string $notes): void;

    /**
     * @param string $promotionTotal
     *
     * @return bool
     */
    public function hasPromotionTotal(string $promotionTotal): bool;

    /**
     * @param string $promotionName
     *
     * @return bool
     */
    public function hasPromotion(string $promotionName): bool;

    /**
     * @param string $promotionName
     *
     * @return bool
     */
    public function hasShippingPromotion(string $promotionName): bool;

    /**
     * @param string $taxTotal
     *
     * @return bool
     */
    public function hasTaxTotal(string $taxTotal): bool;

    /**
     * @param string $price
     *
     * @return bool
     */
    public function hasShippingTotal(string $price): bool;

    /**
     * @param string $price
     *
     * @return bool
     */
    public function hasProductUnitPrice(ProductInterface $product, string $price): bool;

    /**
     * @param string $localeName
     *
     * @return bool
     */
    public function hasLocale(string $localeName): bool;

    /**
     * @param string $currencyCode
     *
     * @return bool
     */
    public function hasCurrency(string $currencyCode): bool;

    /**
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function changeAddress(): void;

    public function changeShippingMethod(): void;

    public function changePaymentMethod(): void;

    public function confirmOrder(): void;

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasShippingProvinceName(string $provinceName): bool;

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasBillingProvinceName(string $provinceName): bool;

    /**
     * @return string
     */
    public function getBaseCurrencyOrderTotal(): string;

    /**
     * @param string $promotionName
     *
     * @return string
     */
    public function getShippingPromotionDiscount(string $promotionName): string;

    /**
     * @return string
     */
    public function getValidationErrors(): string;
}
