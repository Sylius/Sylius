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
    public function hasItemWithProductAndQuantity(string $productName, string $quantity): bool;

    public function hasShippingAddress(AddressInterface $address): bool;

    public function hasBillingAddress(AddressInterface $address): bool;

    public function getPaymentMethodName(): bool;

    public function hasPaymentMethod(): bool;

    public function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool;

    public function hasProductDiscountedUnitPriceBy(ProductInterface $product, float $amount): bool;

    public function hasOrderTotal(float $total): bool;

    public function addNotes(string $notes);

    public function hasPromotionTotal(string $promotionTotal): bool;

    public function hasPromotion(string $promotionName): bool;

    public function hasShippingPromotion(string $promotionName): bool;

    public function hasTaxTotal(string $taxTotal): bool;

    public function hasShippingTotal(string $price): bool;

    public function hasProductUnitPrice(ProductInterface $product, string $price): bool;

    public function hasLocale(string $localeName): bool;

    public function hasCurrency(string $currencyCode): bool;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function changeAddress();

    public function changeShippingMethod();

    public function changePaymentMethod();

    public function confirmOrder();

    public function hasShippingProvinceName(string $provinceName): bool;

    public function hasBillingProvinceName(string $provinceName): bool;

    public function getBaseCurrencyOrderTotal(): string;

    public function getShippingPromotionDiscount(string $promotionName): string;

    public function getValidationErrors(): string;
}
