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

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $customerName
     */
    public function hasCustomer(string $customerName): void;

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    public function hasShippingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool;

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    public function hasBillingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool;

    /**
     * @param string $shippingMethodName
     *
     * @return bool
     */
    public function hasShipment(string $shippingMethodName): bool;

    /**
     * @param string $code
     */
    public function specifyTrackingCode(string $code): void;

    /**
     *
     * @return bool
     */
    public function canShipOrder(OrderInterface $order): bool;

    /**
     * @param OrderInterface $order
     */
    public function shipOrder(OrderInterface $order): void;

    /**
     * @param string $paymentMethodName
     *
     * @return bool
     */
    public function hasPayment(string $paymentMethodName): bool;

    /**
     *
     * @return bool
     */
    public function canCompleteOrderLastPayment(OrderInterface $order): bool;

    /**
     * @param OrderInterface $order
     */
    public function completeOrderLastPayment(OrderInterface $order): void;

    /**
     * @param OrderInterface $order
     */
    public function refundOrderLastPayment(OrderInterface $order): void;

    /**
     * @return int
     */
    public function countItems(): int;

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isProductInTheList(string $productName): bool;

    /**
     * @return string
     */
    public function getItemsTotal(): string;

    /**
     * @return string
     */
    public function getTotal(): string;

    /**
     * @return string
     */
    public function getShippingTotal(): string;

    /**
     * @param string $shippingCharge
     *
     * @return bool
     */
    public function hasShippingCharge(string $shippingCharge): bool;

    /**
     * @return string
     */
    public function getTaxTotal(): string;

    /**
     * @return string
     */
    public function getPromotionTotal(): string;

    /**
     * @param string $promotionDiscount
     *
     * @return bool
     */
    public function hasPromotionDiscount(string $promotionDiscount): bool;

    /**
     * @param string $promotionName
     *
     * @return bool
     */
    public function hasShippingPromotion(string $promotionName): bool;

    /**
     * @param string $tax
     *
     * @return bool
     */
    public function hasTax(string $tax): bool;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemCode(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemUnitPrice(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemDiscountedUnitPrice(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemQuantity(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemDiscount(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemTax(string $itemName): string;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemTotal(string $itemName): string;

    /**
     * @return string
     */
    public function getPaymentAmount(): string;

    /**
     * @return int
     */
    public function getPaymentsCount(): int;

    /**
     * @return int
     */
    public function getShipmentsCount(): int;

    /**
     * @return bool
     */
    public function hasCancelButton(): bool;

    /**
     * @return string
     */
    public function getOrderState(): string;

    /**
     * @return string
     */
    public function getPaymentState(): string;

    /**
     * @return string
     */
    public function getShippingState(): string;

    public function cancelOrder(): void;

    public function deleteOrder(): void;

    /**
     * @param string $note
     *
     * @return bool
     */
    public function hasNote(string $note): bool;

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
    public function getIpAddressAssigned(): string;

    /**
     * @return string
     */
    public function getOrderCurrency(): string;

    /**
     * @return bool
     */
    public function hasRefundButton(): bool;

    /**
     * @return string
     */
    public function getShippingPromotionData(): string;
}
