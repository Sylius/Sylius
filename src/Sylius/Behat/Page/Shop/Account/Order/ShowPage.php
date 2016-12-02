<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account\Order;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableAccessorInterface $tableAccessor
     */
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
    public function getRouteName()
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

        return trim(str_replace('Subtotal:', '', $totalElement->getText()));
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

    /**
     * {@inheritdoc}
     */
    public function isProductInTheList($name)
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields(
                $this->getElement('order_items'),
                ['item' => $name]
            );

            return 1 === count($rows);
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
    protected function getDefinedElements()
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
            (stripos($elementText, $city.', '.$postcode) !== false) &&
            (stripos($elementText, $countryName) !== false)
        ;
    }
}
