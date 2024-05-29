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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private TableAccessorInterface $tableAccessor,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function hasCustomer(string $customerEmail): bool
    {
        return 0 === strcasecmp($customerEmail, $this->getElement('customer_email')->getText());
    }

    public function hasShippingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return $this->hasAddress($shippingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    public function hasShippingAddressVisible(): bool
    {
        try {
            $this->getElement('shipping_address');
        } catch (ElementNotFoundException) {
            return false;
        }

        return true;
    }

    public function hasBillingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return $this->hasAddress($billingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    public function hasShipment(string $shippingMethodName): bool
    {
        foreach ($this->getElement('shipments')->findAll('css', '[data-test-shipment-method]') as $shipmentMethod) {
            if (0 === strcasecmp($shippingMethodName, $shipmentMethod->getText())) {
                return true;
            }
        }

        return false;
    }

    public function hasShipmentWithState(string $state): bool
    {
        foreach ($this->getElement('shipments')->findAll('css', '[data-test-shipment-state]') as $shipmentState) {
            if (0 === strcasecmp($state, $shipmentState->getText())) {
                return true;
            }
        }

        return false;
    }

    public function specifyTrackingCode(string $code): void
    {
        $this->getElement('shipment_tracking')->setValue($code);
    }

    public function canShipOrder(OrderInterface $order): bool
    {
        return $this->hasElement('shipment_ship_button');
    }

    public function shipOrder(OrderInterface $order): void
    {
        $this->getElement('shipment_ship_button')->press();
    }

    public function hasPayment(string $paymentMethodName): bool
    {
        foreach ($this->getElement('payments')->findAll('css', '[data-test-payment-method]') as $paymentMethod) {
            if (0 === strcasecmp($paymentMethodName, $paymentMethod->getText())) {
                return true;
            }
        }

        return false;
    }

    public function hasPaymentWithState(string $state): bool
    {
        foreach ($this->getElement('payments')->findAll('css', '[data-test-payment-state]') as $paymentState) {
            if (0 === strcasecmp($state, $paymentState->getText())) {
                return true;
            }
        }

        return false;
    }

    public function canCompleteOrderLastPayment(OrderInterface $order): bool
    {
        $lastPayment = $order->getLastPayment();

        return $this->hasElement('payment_complete', ['%paymentId%' => $lastPayment->getId()]);
    }

    public function completeOrderLastPayment(OrderInterface $order): void
    {
        $lastPayment = $order->getLastPayment();

        $this->getElement('payment_complete', ['%paymentId%' => $lastPayment->getId()])->submit();
    }

    public function refundOrderLastPayment(OrderInterface $order): void
    {
        $lastPayment = $order->getLastPayment();

        $this->getElement('payment_refund', ['%paymentId%' => $lastPayment->getId()])->submit();
    }

    public function countItems(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('table-items'));
    }

    public function isProductInTheList(string $productName): bool
    {
        return null !== $this->getRowWithItem($productName);
    }

    public function getItemsTotal(): string
    {
        return $this->getElement('items_total')->getText();
    }

    public function getTotal(): string
    {
        return $this->getElement('order_total')->getText();
    }

    public function getShippingTotal(): string
    {
        return $this->getElement('shipping_total')->getText();
    }

    public function getTaxTotal(): string
    {
        $taxTotalElement = $this->getElement('tax_total');

        return trim(str_replace('Tax total:', '', $taxTotalElement->getText()));
    }

    public function hasShippingCharge(string $shippingCharge, string $shippingMethodName): bool
    {
        $shipping = $this->getElement('shipping', ['%name%' => $shippingMethodName]);

        return 0 === strcasecmp($shippingCharge, $shipping->find('css', '[data-test-base-value]')->getText());
    }

    public function hasShippingTax(string $shippingTax, string $shippingMethodName): bool
    {
        $shipping = $this->getElement('shipping', ['%name%' => $shippingMethodName]);

        return 0 === strcasecmp($shippingTax, $shipping->find('css', '[data-test-tax-value]')->getText());
    }

    public function getOrderPromotionTotal(): string
    {
        return $this->getElement('promotion_total')->getText();
    }

    public function hasPromotionDiscount(string $promotionName, string $promotionAmount): bool
    {
        $promotion = $this->getElement('promotion', ['%name%' => $promotionName]);

        return 0 === strcasecmp($promotionAmount, $promotion->find('css', '[data-test-discount]')->getText());
    }

    public function hasTax(string $tax): bool
    {
        $taxesText = $this->getElement('taxes')->getText();

        return stripos($taxesText, $tax) !== false;
    }

    public function getItemCode(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-code]')->getText();
    }

    public function getItemUnitPrice(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-unit-price]')->getText();
    }

    public function getItemDiscountedUnitPrice(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-discounted-unit-price]')->getText();
    }

    public function getItemOrderDiscount(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-distributed-order-discount]')->getText();
    }

    public function getItemQuantity(string $itemName): string
    {
        return  $this->getRowWithItem($itemName)->find('css', '[data-test-quantity]')->getText();
    }

    public function getItemSubtotal(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-subtotal]')->getText();
    }

    public function getItemDiscount(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-unit-discount]')->getText();
    }

    public function getItemTax(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-tax-excluded]')->getText();
    }

    public function getItemTaxIncludedInPrice(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-tax-included]')->getText();
    }

    public function getItemTotal(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '[data-test-total]')->getText();
    }

    public function getPaymentAmount(): string
    {
        $paymentsPrice = $this->getElement('payments')->find('css', '[data-test-payment-amount]');

        return $paymentsPrice->getText();
    }

    public function getPaymentsCount(): int
    {
        $payments = $this->getElement('payments')->findAll('css', '[data-test-payment]');

        return count($payments);
    }

    public function getShipmentsCount(): int
    {
        try {
            $shipments = $this->getElement('shipments')->findAll('css', '.item');
        } catch (ElementNotFoundException) {
            return 0;
        }

        return count($shipments);
    }

    public function hasCancelButton(): bool
    {
        return $this->hasElement('cancel_order');
    }

    public function cancelOrder(): void
    {
        $this->getElement('cancel_order')->click();
    }

    public function getOrderState(): string
    {
        return $this->getElement('order_state')->getText();
    }

    public function getPaymentState(): string
    {
        return $this->getElement('order_payment_state')->getText();
    }

    public function getShippingState(): string
    {
        return $this->getElement('order_shipping_state')->getText();
    }

    public function deleteOrder(): void
    {
        $this->getDocument()->pressButton('Delete');
    }

    public function hasNote(string $note): bool
    {
        $orderNotesElement = $this->getElement('order_notes');

        return $orderNotesElement->getText() === $note;
    }

    public function hasShippingProvinceName(string $provinceName): bool
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return false !== stripos($shippingAddressText, $provinceName);
    }

    public function hasBillingProvinceName(string $provinceName): bool
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return false !== stripos($billingAddressText, $provinceName);
    }

    public function getIpAddressAssigned(): string
    {
        return $this->getElement('ip_address')->getText();
    }

    public function getOrderCurrency(): string
    {
        return $this->getElement('currency')->getText();
    }

    public function hasRefundButton(): bool
    {
        return $this->getDocument()->hasButton('Refund');
    }

    public function getShippingPromotionData(): string
    {
        return $this->getElement('shipping_promotion_discount')->getText();
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_order_show';
    }

    public function hasInformationAboutNoPayment(): bool
    {
        return $this->getElement('payments')->has('css', '[data-test-no-payments]');
    }

    public function resendOrderConfirmationEmail(): void
    {
        $this->getElement('resend_order_confirmation_email')->click();
    }

    public function isResendOrderConfirmationEmailButtonVisible(): bool
    {
        return $this->getDocument()->has('css', '[data-test-resend-order-confirmation-email]');
    }

    public function resendShipmentConfirmationEmail(): void
    {
        $this->getElement('resend_shipment_confirmation_email')->click();
    }

    public function isResendShipmentConfirmationEmailButtonVisible(): bool
    {
        return $this->getDocument()->has('css', '[data-test-resend-shipment-confirmation-email]');
    }

    public function getShippedAtDate(): string
    {
        return  $this->getElement('shipment_shipped_at_date')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address' => '[data-test-billing-address]',
            'cancel_order' => '[data-test-cancel-order]',
            'currency' => '#sylius-order-currency',
            'customer_email' => '[data-test-customer] [data-test-email]',
            'ip_address' => '#ipAddress',
            'item' => '[data-test-item="%name%"]',
            'items_total' => '[data-test-items-total]',
            'order_notes' => '#sylius-order-notes',
            'order_payment_state' => '[data-test-order-payment-state]',
            'order_shipping_state' => '[data-test-order-shipping-state]',
            'order_state' => '[data-test-order-state]',
            'order_total' => '[data-test-order-total]',
            'payments' => '[data-test-payments]',
            'payment_complete' => '[data-test-complete-payment="%paymentId%"]',
            'payment_refund' => '[data-test-refund-payment="%paymentId%"]',
            'promotion' => '[data-test-promotion="%name%"]',
            'promotion_total' => '[data-test-promotion-total]',
            'resend_order_confirmation_email' => '[data-test-resend-order-confirmation-email]',
            'resend_shipment_confirmation_email' => '[data-test-resend-shipment-confirmation-email]',
            'shipment_shipped_at_date' => '[data-test-shipments] [data-test-shipped-at-date]',
            'shipments' => '[data-test-shipments]',
            'shipment_tracking' => '[data-test-shipment-tracking]',
            'shipment_ship_button' => '[data-test-shipment-ship-button]',
            'shipping' => '[data-test-shipping="%name%"]',
            'shipping_address' => '[data-test-shipping-address]',
            'shipping_adjustment_name' => '#shipping-adjustment-label',
            'shipping_promotion_discount' => '[data-test-shipping-promotion-discount]',
            'shipping_tax' => '#shipping-tax-value',
            'shipping_total' => '[data-test-shipping-total]',
            'table-items' => '[data-test-table-items]',
            'tax_total' => '[data-test-tax-total]',
            'taxes' => '#taxes',
        ]);
    }

    protected function getTableAccessor(): TableAccessorInterface
    {
        return $this->tableAccessor;
    }

    protected function hasAddress(string $elementText, string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        return
            (stripos($elementText, $customerName) !== false) &&
            (stripos($elementText, $street) !== false) &&
            (stripos($elementText, $city) !== false) &&
            (stripos($elementText, $countryName . ' ' . $postcode) !== false)
        ;
    }

    protected function getRowWithItem(string $itemName): ?NodeElement
    {
        return $this->getElement('item', ['%name%' => $itemName]);
    }
}
