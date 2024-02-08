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

namespace Sylius\Behat\Page\Shop\Account\Order;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
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

    public function getRouteName(): string
    {
        return 'sylius_shop_account_order_show';
    }

    public function getNumber(): string
    {
        $numberText = $this->getElement('number')->getText();
        $numberText = str_replace('#', '', $numberText);

        return $numberText;
    }

    public function hasShippingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): bool {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return $this->hasAddress($shippingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    public function hasBillingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): bool {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return $this->hasAddress($billingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    public function choosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $paymentMethodElement = $this->getElement('payment_method', ['%name%' => $paymentMethod->getName()]);
        $paymentMethodElement->selectOption($paymentMethodElement->getAttribute('value'));
    }

    public function pay(): void
    {
        $this->getElement('pay_link')->click();
    }

    public function getChosenPaymentMethod(): string
    {
        $paymentMethodItems = $this->getDocument()->findAll('css', '[data-test-payment-item]');

        foreach ($paymentMethodItems as $method) {
            if ($method->find('css', '[data-test-payment-method-select]')->hasAttribute('checked')) {
                return $method->find('css', 'a')->getText();
            }
        }

        return '';
    }

    public function getTotal(): string
    {
        $totalElement = $this->getElement('total');

        return trim(str_replace('Total:', '', $totalElement->getText()));
    }

    public function getSubtotal(): string
    {
        $totalElement = $this->getElement('subtotal');

        return trim(str_replace('Items total:', '', $totalElement->getText()));
    }

    public function getOrderShipmentStatus(): string
    {
        return $this->getElement('order_shipment_state')->getText();
    }

    public function getShipmentStatus(): string
    {
        return $this->getElement('shipment_state')->getText();
    }

    public function countItems(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('order_items'));
    }

    public function getPaymentPrice(): string
    {
        return $this->getElement('payment_price')->getText();
    }

    public function getPaymentStatus(): string
    {
        return $this->getElement('payment_state')->getText();
    }

    public function getOrderPaymentStatus(): string
    {
        return $this->getElement('order_payment_state')->getText();
    }

    public function isProductInTheList(string $productName): bool
    {
        return $this->hasElement('product_name', ['%productName%' => $productName]);
    }

    public function getItemPrice(): string
    {
        return $this->getElement('product_price')->getText();
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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address' => '[data-test-billing-address]',
            'number' => '[data-test-order-number]',
            'order_items' => '[data-test-order-table]',
            'order_payment_state' => '[data-test-order-payment-state]',
            'order_shipment_state' => '[data-test-order-shipment-state]',
            'pay_link' => '[data-test-pay-link]',
            'payment_method' => '[data-test-payment-item]:contains("%name%") [data-test-payment-method-select]',
            'payment_price' => '[data-test-payment-price]',
            'payment_state' => '[data-test-payment-state]',
            'product_name' => '[data-test-order-table] [data-test-product-name="%productName%"]',
            'product_price' => '[data-test-order-table] td:nth-child(2)',
            'shipment_state' => '[data-test-shipment-state]',
            'shipping_address' => '[data-test-shipping-address]',
            'subtotal' => '[data-test-subtotal]',
            'total' => '[data-test-order-total]',
        ]);
    }

    private function hasAddress(
        string $elementText,
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): bool {
        return
            (stripos($elementText, $customerName) !== false) &&
            (stripos($elementText, $street) !== false) &&
            (stripos($elementText, $city . ', ' . $postcode) !== false) &&
            (stripos($elementText, $countryName) !== false)
        ;
    }
}
