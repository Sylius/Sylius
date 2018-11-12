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
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * @var TableAccessorInterface
     */
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
    public function getNumber(): string
    {
        $numberText = $this->getElement('number')->getText();
        $numberText = str_replace('#', '', $numberText);

        return $numberText;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return $this->hasAddress($shippingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        $billingAddressText = $this->getElement('billing_address')->getText();

        return $this->hasAddress($billingAddressText, $customerName, $street, $postcode, $city, $countryName);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(): string
    {
        $totalElement = $this->getElement('total');

        return trim(str_replace('Total:', '', $totalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal(): string
    {
        $totalElement = $this->getElement('subtotal');

        return trim(str_replace('Subtotal:', '', $totalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function countItems(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('order_items'));
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentPrice(): string
    {
        $paymentsPrice = $this->getElement('payments')->find('css', 'p');

        return $paymentsPrice->getText();
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
    public function getItemPrice(): string
    {
        return $this->getElement('product_price')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingProvinceName(string $provinceName): bool
    {
        $shippingAddressText = $this->getElement('shipping_address')->getText();

        return false !== stripos($shippingAddressText, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingProvinceName(string $provinceName): bool
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
            'payments' => '#sylius-payments',
            'product_price' => '#sylius-order td:nth-child(2)',
            'subtotal' => '#subtotal',
            'total' => '#total',
        ]);
    }

    private function hasAddress(string $elementText, string $customerName, string $street, string $postcode, string $city, string $countryName): bool
    {
        return
            (stripos($elementText, $customerName) !== false) &&
            (stripos($elementText, $street) !== false) &&
            (stripos($elementText, $city . ', ' . $postcode) !== false) &&
            (stripos($elementText, $countryName) !== false)
        ;
    }
}
