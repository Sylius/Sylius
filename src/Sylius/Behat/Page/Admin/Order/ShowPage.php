<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
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
    public function hasCustomer($customerName)
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $customerName) !== false;
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
    public function hasShipment($shippingMethodName)
    {
        $shipmentsText = $this->getElement('shipments')->getText();

        return stripos($shipmentsText, $shippingMethodName) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPayment($paymentMethodName)
    {
        $paymentsText = $this->getElement('payments')->getText();

        return stripos($paymentsText, $paymentMethodName) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('table'));
    }

    /**
     * {@inheritdoc}
     */
    public function isProductInTheList($productName)
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields(
                $this->getElement('table'),
                ['item' => $productName]
            );

            return 1 === count($rows);

        } catch (\InvalidArgumentException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsTotal()
    {
        $itemsTotalElement = $this->getElement('items_total');

        return trim(str_replace('Items total:', '', $itemsTotalElement->getText()));
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
    public function getShippingTotal()
    {
        $shippingTotalElement = $this->getElement('shipping_total');

        return trim(str_replace('Shipping total:', '', $shippingTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotalElement = $this->getElement('tax_total');

        return trim(str_replace('Tax total:', '', $taxTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingCharge($shippingCharge)
    {
        $shippingChargesText = $this->getElement('shipping_charges')->getText();

        return stripos($shippingChargesText, $shippingCharge) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionTotal()
    {
        $promotionTotalElement = $this->getElement('promotion_total');

        return trim(str_replace('Promotion total:', '', $promotionTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotionDiscount($promotionDiscount)
    {
        $promotionDiscountsText = $this->getElement('promotion_discounts')->getText();

        return stripos($promotionDiscountsText, $promotionDiscount) !== false;
    }

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemDiscount($itemName)
    {
        $rows = $this->tableAccessor->getRowsWithFields(
            $this->getElement('table'),
            ['item' => $itemName]
        );

        $discount = $rows[0]->find('css', '.discount')->getText();

        return $discount;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_order_show';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'customer' => '#customer',
            'shipping_address' => '#shipping-address',
            'billing_address' => '#billing-address',
            'payments' => '#payments',
            'shipments' => '#shipments',
            'table' => '.table',
            'items_total' => '#items-total',
            'total' => '#total',
            'shipping_total' => '#shipping-total',
            'shipping_charges' => '#shipping-charges',
            'tax_total' => '#tax-total',
            'promotion_total' => '#promotion-total',
            'promotion_discounts' => '#promotion-discounts',
        ]);
    }

    /**
     * @return TableAccessorInterface
     */
    protected function getTableAccessor()
    {
        return $this->tableAccessor;
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
            (stripos($elementText, $city) !== false) &&
            (stripos($elementText, $countryName.' '.$postcode) !== false)
        ;
    }
}
