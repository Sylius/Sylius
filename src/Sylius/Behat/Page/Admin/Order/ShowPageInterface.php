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

namespace Sylius\Behat\Page\Admin\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function hasCustomer(string $customerName): bool;

    public function hasShippingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool;

    public function hasShippingAddressVisible(): bool;

    public function hasBillingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool;

    public function hasShipment(string $shippingMethodName): bool;

    public function specifyTrackingCode(string $code): void;

    public function canShipOrder(OrderInterface $order): bool;

    public function shipOrder(OrderInterface $order): void;

    public function hasPayment(string $paymentMethodName): bool;

    public function canCompleteOrderLastPayment(OrderInterface $order): bool;

    public function completeOrderLastPayment(OrderInterface $order): void;

    public function refundOrderLastPayment(OrderInterface $order): void;

    public function countItems(): int;

    public function isProductInTheList(string $productName): bool;

    public function getItemsTotal(): string;

    public function getTotal(): string;

    public function getShippingTotal(): string;

    public function hasShippingCharge(string $shippingCharge, string $shippingMethodName): bool;

    public function hasShippingTax(string $shippingTax, string $shippingMethodName): bool;

    public function getTaxTotal(): string;

    public function getOrderPromotionTotal(): string;

    public function hasPromotionDiscount(string $promotionName, string $promotionAmount): bool;

    public function hasTax(string $tax): bool;

    public function getItemCode(string $itemName): string;

    public function getItemUnitPrice(string $itemName): string;

    public function getItemDiscountedUnitPrice(string $itemName): string;

    public function getItemQuantity(string $itemName): string;

    public function getItemSubtotal(string $itemName): string;

    public function getItemDiscount(string $itemName): string;

    public function getItemTax(string $itemName): string;

    public function getItemTaxIncludedInPrice(string $itemName): string;

    public function getItemTotal(string $itemName): string;

    public function getPaymentAmount(): string;

    public function getPaymentsCount(): int;

    public function getShipmentsCount(): int;

    public function hasCancelButton(): bool;

    public function getOrderState(): string;

    public function getPaymentState(): string;

    public function getShippingState(): string;

    public function cancelOrder(): void;

    public function deleteOrder(): void;

    public function hasNote(string $note): bool;

    public function hasShippingProvinceName(string $provinceName): bool;

    public function hasBillingProvinceName(string $provinceName): bool;

    public function getIpAddressAssigned(): string;

    public function getOrderCurrency(): string;

    public function hasRefundButton(): bool;

    public function getShippingPromotionData(): string;

    public function getItemOrderDiscount(string $itemName): string;

    public function hasInformationAboutNoPayment(): bool;

    public function resendOrderConfirmationEmail(): void;

    public function isResendOrderConfirmationEmailButtonVisible(): bool;

    public function resendShipmentConfirmationEmail(): void;

    public function isResendShipmentConfirmationEmailButtonVisible(): bool;

    public function getShippedAtDate(): string;
}
