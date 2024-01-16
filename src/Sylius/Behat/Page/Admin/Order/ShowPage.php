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
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private TableAccessorInterface $tableAccessor,
        private MoneyFormatterInterface $moneyFormatter,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function hasCustomer(string $customerName): bool
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $customerName) !== false;
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

    public function hasShipment(string $shippingDetails): bool
    {
        $shipmentsText = $this->getElement('shipments')->getText();

        return stripos($shipmentsText, $shippingDetails) !== false;
    }

    public function specifyTrackingCode(string $code): void
    {
        $this->getDocument()->fillField('sylius_shipment_ship_tracking', $code);
    }

    public function canShipOrder(OrderInterface $order): bool
    {
        return $this->getLastOrderShipmentElement($order)->hasButton('Ship');
    }

    public function shipOrder(OrderInterface $order): void
    {
        $this->getLastOrderShipmentElement($order)->pressButton('Ship');
    }

    public function hasPayment(string $paymentDetails): bool
    {
        $paymentsText = $this->getElement('payments')->getText();

        return stripos($paymentsText, $paymentDetails) !== false;
    }

    public function canCompleteOrderLastPayment(OrderInterface $order): bool
    {
        return $this->getLastOrderPaymentElement($order)->hasButton('Complete');
    }

    public function completeOrderLastPayment(OrderInterface $order): void
    {
        $this->getLastOrderPaymentElement($order)->pressButton('Complete');
    }

    public function refundOrderLastPayment(OrderInterface $order): void
    {
        $this->getLastOrderPaymentElement($order)->pressButton('Refund');
    }

    public function countItems(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('table'));
    }

    public function isProductInTheList(string $productName): bool
    {
        try {
            $table = $this->getElement('table');
            $rows = $this->tableAccessor->getRowsWithFields(
                $table,
                ['item' => $productName],
            );

            foreach ($rows as $row) {
                $field = $this->tableAccessor->getFieldFromRow($table, $row, 'item');
                $name = $field->find('css', '.sylius-product-name');
                if (null !== $name && $name->getText() === $productName) {
                    return true;
                }
            }

            return false;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function getItemsTotal(): string
    {
        $itemsTotalElement = $this->getElement('items_total');

        return trim(str_replace('Items total:', '', $itemsTotalElement->getText()));
    }

    public function getTotal(): string
    {
        $totalElement = $this->getElement('total');

        return trim(str_replace('Order total:', '', $totalElement->getText()));
    }

    public function getShippingTotal(): string
    {
        $shippingTotalElement = $this->getElement('shipping_total');

        return trim(str_replace('Shipping total:', '', $shippingTotalElement->getText()));
    }

    public function getTaxTotal(): string
    {
        $taxTotalElement = $this->getElement('tax_total');

        return trim(str_replace('Tax total:', '', $taxTotalElement->getText()));
    }

    public function hasShippingCharge(string $shippingCharge, string $shippingMethodName): bool
    {
        $shippingBaseValues = $this->getDocument()->findAll('css', '#shipping-base-value');
        foreach ($shippingBaseValues as $shippingBaseValueElement) {
            $shippingBaseValueWithLabel = $shippingBaseValueElement->getParent()->getText();
            if (stripos($shippingBaseValueWithLabel, $shippingCharge) !== false &&
                stripos($shippingBaseValueWithLabel, $shippingMethodName) !== false
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasShippingTax(string $shippingTax, string $shippingMethodName): bool
    {
        $shippingTaxes = $this->getDocument()->findAll('css', '#shipping-tax-value');
        foreach ($shippingTaxes as $shippingTaxElement) {
            $shippingTaxWithLabel = $shippingTaxElement->getParent()->getParent()->getText();
            if (stripos($shippingTaxWithLabel, $shippingTax) !== false &&
                stripos($shippingTaxWithLabel, $shippingMethodName) !== false
            ) {
                return true;
            }
        }

        return false;
    }

    public function getOrderPromotionTotal(): string
    {
        $promotionTotalElement = $this->getElement('promotion_total');

        return trim(str_replace('Promotion total:', '', $promotionTotalElement->getText()));
    }

    public function hasPromotionDiscount(string $promotionName, string $promotionAmount): bool
    {
        $promotionDiscountsText = $this->getElement('promotion_discounts')->getText();

        return stripos($promotionDiscountsText, sprintf('%s %s', $promotionAmount, $promotionName)) !== false;
    }

    public function hasTax(string $tax): bool
    {
        $taxesText = $this->getElement('taxes')->getText();

        return stripos($taxesText, $tax) !== false;
    }

    public function getItemCode(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'sylius-product-variant-code');
    }

    public function getItemUnitPrice(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '.unit-price')->getText();
    }

    public function getItemDiscountedUnitPrice(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '.discounted-unit-price')->getText();
    }

    public function getItemOrderDiscount(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '.unit-order-discount')->getText();
    }

    public function getItemQuantity(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'quantity');
    }

    public function getItemSubtotal(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'subtotal');
    }

    public function getItemDiscount(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'unit-discount');
    }

    public function getItemTax(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '.tax-excluded')->getText();
    }

    public function getItemTaxIncludedInPrice(string $itemName): string
    {
        return $this->getRowWithItem($itemName)->find('css', '.tax-included')->getText();
    }

    public function getItemTotal(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'total');
    }

    public function getPaymentAmount(): string
    {
        $paymentsPrice = $this->getElement('payments')->find('css', '.description');

        return $paymentsPrice->getText();
    }

    public function getPaymentsCount(): int
    {
        try {
            $payments = $this->getElement('payments')->findAll('css', '.item');
        } catch (ElementNotFoundException) {
            return 0;
        }

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
        return $this->getDocument()->hasButton('Cancel');
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

    public function cancelOrder(): void
    {
        $this->getDocument()->pressButton('Cancel');
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
        return $this->getElement('promotion_shipping_discounts')->getText();
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_order_show';
    }

    public function hasInformationAboutNoPayment(): bool
    {
        return $this->getDocument()->has('css', '#no-payments:contains("Order without payments")');
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
            'billing_address' => '#billing-address',
            'currency' => '#sylius-order-currency',
            'customer' => '#customer',
            'ip_address' => '#ipAddress',
            'items_total' => '#items-total',
            'order_notes' => '#sylius-order-notes',
            'order_payment_state' => '#payment-state > span',
            'order_shipping_state' => '#shipping-state > span',
            'order_state' => '#sylius-order-state',
            'payments' => '#sylius-payments',
            'promotion_discounts' => '#promotion-discounts',
            'promotion_shipping_discounts' => '#shipping-discount-value',
            'promotion_total' => '#promotion-total',
            'resend_order_confirmation_email' => '[data-test-resend-order-confirmation-email]',
            'resend_shipment_confirmation_email' => '[data-test-resend-shipment-confirmation-email]',
            'shipment_shipped_at_date' => '#sylius-shipments .shipped-at-date',
            'shipments' => '#sylius-shipments',
            'shipping_address' => '#shipping-address',
            'shipping_adjustment_name' => '#shipping-adjustment-label',
            'shipping_charges' => '#shipping-base-value',
            'shipping_tax' => '#shipping-tax-value',
            'shipping_total' => '#shipping-total',
            'table' => '.table',
            'tax_total' => '#tax-total',
            'taxes' => '#taxes',
            'total' => '#total',
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

    protected function getItemProperty(string $itemName, string $property): string
    {
        $rows = $this->tableAccessor->getRowsWithFields(
            $this->getElement('table'),
            ['item' => $itemName],
        );

        return $rows[0]->find('css', '.' . $property)->getText();
    }

    protected function getRowWithItem(string $itemName): ?NodeElement
    {
        return $this->tableAccessor->getRowWithFields($this->getElement('table'), ['item' => $itemName]);
    }

    protected function getLastOrderPaymentElement(OrderInterface $order): ?NodeElement
    {
        $payment = $order->getPayments()->last();

        $paymentStateElements = $this->getElement('payments')->findAll('css', sprintf('span.ui.label:contains(\'%s\')', ucfirst($payment->getState())));
        $paymentStateElement = end($paymentStateElements);

        return $paymentStateElement->getParent()->getParent();
    }

    protected function getLastOrderShipmentElement(OrderInterface $order): ?NodeElement
    {
        $shipment = $order->getShipments()->last();

        $shipmentStateElements = $this->getElement('shipments')->findAll('css', sprintf('span.ui.label:contains(\'%s\')', ucfirst($shipment->getState())));
        $shipmentStateElement = end($shipmentStateElements);

        return $shipmentStateElement->getParent()->getParent();
    }

    protected function getFormattedMoney(int $orderPromotionTotal): string
    {
        return $this->moneyFormatter->format($orderPromotionTotal, $this->getDocument()->find('css', '#sylius-order-currency')->getText());
    }
}
