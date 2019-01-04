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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
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
                ['item' => $productName]
            );

            foreach ($rows as $row) {
                $field = $this->tableAccessor->getFieldFromRow($table, $row, 'item');
                $name = $field->find('css', '.sylius-product-name');
                if (null !== $name && $name->getText() === $productName) {
                    return true;
                }
            }

            return false;
        } catch (\InvalidArgumentException $exception) {
            return false;
        }
    }

    public function getItemsTotal(): string
    {
        $itemsTotalElement = $this->getElement('items_total');

        return trim(str_replace('Subtotal:', '', $itemsTotalElement->getText()));
    }

    public function getTotal(): string
    {
        $totalElement = $this->getElement('total');

        return trim(str_replace('Total:', '', $totalElement->getText()));
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

    public function hasShippingCharge(string $shippingCharge): bool
    {
        $shippingChargesText = $this->getElement('shipping_charges')->getText();

        return stripos($shippingChargesText, $shippingCharge) !== false;
    }

    public function getPromotionTotal(): string
    {
        $promotionTotalElement = $this->getElement('promotion_total');

        return trim(str_replace('Promotion total:', '', $promotionTotalElement->getText()));
    }

    public function hasPromotionDiscount(string $promotionDiscount): bool
    {
        $promotionDiscountsText = $this->getElement('promotion_discounts')->getText();

        return stripos($promotionDiscountsText, $promotionDiscount) !== false;
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
        return $this->getItemProperty($itemName, 'unit-price');
    }

    public function getItemDiscountedUnitPrice(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'discounted-unit-price');
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
        return $this->getItemProperty($itemName, 'discount');
    }

    public function getItemTax(string $itemName): string
    {
        return $this->getItemProperty($itemName, 'tax');
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
        } catch (ElementNotFoundException $exception) {
            return 0;
        }

        return count($payments);
    }

    public function getShipmentsCount(): int
    {
        try {
            $shipments = $this->getElement('shipments')->findAll('css', '.item');
        } catch (ElementNotFoundException $exception) {
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
            'promotion_shipping_discounts' => '#promotion-shipping-discounts',
            'promotion_total' => '#promotion-total',
            'shipments' => '#sylius-shipments',
            'shipping_address' => '#shipping-address',
            'shipping_charges' => '#shipping-charges',
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

    private function hasAddress(string $elementText, string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        return
            (stripos($elementText, $customerName) !== false) &&
            (stripos($elementText, $street) !== false) &&
            (stripos($elementText, $city) !== false) &&
            (stripos($elementText, $countryName . ' ' . $postcode) !== false)
        ;
    }

    private function getItemProperty(string $itemName, string $property): string
    {
        $rows = $this->tableAccessor->getRowsWithFields(
            $this->getElement('table'),
            ['item' => $itemName]
        );

        return $rows[0]->find('css', '.' . $property)->getText();
    }

    private function getLastOrderPaymentElement(OrderInterface $order): ?NodeElement
    {
        $payment = $order->getPayments()->last();

        $paymentStateElements = $this->getElement('payments')->findAll('css', sprintf('span.ui.label:contains(\'%s\')', ucfirst($payment->getState())));
        $paymentStateElement = end($paymentStateElements);

        return $paymentStateElement->getParent()->getParent();
    }

    private function getLastOrderShipmentElement(OrderInterface $order): ?NodeElement
    {
        $shipment = $order->getShipments()->last();

        $shipmentStateElements = $this->getElement('shipments')->findAll('css', sprintf('span.ui.label:contains(\'%s\')', ucfirst($shipment->getState())));
        $shipmentStateElement = end($shipmentStateElements);

        return $shipmentStateElement->getParent()->getParent();
    }
}
