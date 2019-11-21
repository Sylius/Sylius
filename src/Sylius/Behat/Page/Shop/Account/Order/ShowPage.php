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

namespace Sylius\Behat\Page\Shop\Account\Order;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $minkParameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_account_order_show';
    }

    /**
     * {@inheritdoc}
     */
    public function getNumber()
    {
        $numberText = $this->getElement('number')->getText();
        $numberText = str_replace('#', '', $numberText);

        return $numberText;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingAddress($customerName, $street, $postcode, $city, $countryName)
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return $this->hasAddress($shippingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    public function getOrderShipmentStatus(): string
    {
        return $this->getElement('order_shipment_status')->getText();
    }

    public function getShipmentStatus(): string
    {
        return $this->getElement('shipment_status')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingAddress($customerName, $street, $postcode, $city, $countryName)
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return $this->hasAddress($billingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        $totalElement = $this->getElement('total');

        return trim(str_replace('Total:', '', $totalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal()
    {
        $totalElement = $this->getElement('subtotal');

        return trim(str_replace('Items total:', '', $totalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('order_items'));
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentPrice()
    {
        $paymentsPrice = $this->getElement('payments')->find('css', 'p');

        return $paymentsPrice->getText();
    }

    public function getPaymentStatus(): string
    {
        return $this->getElement('payment_status')->getText();
    }

    public function getOrderPaymentStatus(): string
    {
        return $this->getElement('order_payment_status')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function isProductInTheList(string $productName): bool
    {
        try {
            $table = $this->getElement('order_items');
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

    /**
     * {@inheritdoc}
     */
    public function getItemPrice()
    {
        return $this->getElement('product_price')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingProvinceName($provinceName)
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return false !== stripos($shippingAddressText, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingProvinceName($provinceName)
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return false !== stripos($billingAddressText, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address' => '#sylius-billing-address',
            'shipping_address' => '#sylius-shipping-address',
            'number' => '#number',
            'order_items' => '#sylius-order',
            'order_payment_status' => '#order-payment-status',
            'order_shipment_status' => '#order-shipment-status',
            'payment_status' => '#payment-status',
            'payments' => '#sylius-payments',
            'product_price' => '#sylius-order td:nth-child(2)',
            'shipment_status' => '#shipment-status',
            'subtotal' => '#subtotal',
            'total' => '#total',
        ]);
    }

    /**
     * @param string $elementText
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    private function hasAddress($elementText, $customerName, $street, $postcode, $city, $countryName)
    {
        return
            (stripos($elementText, $customerName) !== false) &&
            (stripos($elementText, $street) !== false) &&
            (stripos($elementText, $city . ', ' . $postcode) !== false) &&
            (stripos($elementText, $countryName) !== false)
        ;
    }
}
