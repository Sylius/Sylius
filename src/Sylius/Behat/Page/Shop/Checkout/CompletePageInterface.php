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

namespace Sylius\Behat\Page\Shop\Checkout;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

interface CompletePageInterface extends SymfonyPageInterface
{
    public function hasItemWithProductAndQuantity(string $productName, string $quantity): bool;

    public function hasShippingAddress(AddressInterface $address): bool;

    public function hasBillingAddress(AddressInterface $address): bool;

    public function getPaymentMethodName(): string;

    public function hasPaymentMethod(): bool;

    public function getProductUnitPrice(ProductInterface $product): int;

    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, int $amount): bool;

    public function hasOrderTotal(int $total): bool;

    public function getBaseCurrencyOrderTotal(): string;

    public function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool;

    public function addNotes(string $notes): void;

    public function hasPromotionTotal(string $promotionTotal): bool;

    public function hasPromotion(string $promotionName): bool;

    public function hasShippingPromotion(string $promotionName): bool;

    public function getTaxTotal(): string;

    public function getShippingTotal(): string;

    public function hasShippingTotal(): bool;

    public function hasProductUnitPrice(ProductInterface $product, string $price): bool;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function getValidationErrors(): string;

    public function hasLocale(string $localeName): bool;

    public function hasCurrency(string $currencyCode): bool;

    public function confirmOrder(): void;

    public function changeAddress(): void;

    public function changeShippingMethod(): void;

    public function changePaymentMethod(): void;

    public function hasShippingProvinceName(string $provinceName): bool;

    public function hasBillingProvinceName(string $provinceName): bool;

    public function hasShippingPromotionWithDiscount(string $promotionName, string $discount): bool;

    public function hasOrderPromotion(string $promotionName): bool;
}
