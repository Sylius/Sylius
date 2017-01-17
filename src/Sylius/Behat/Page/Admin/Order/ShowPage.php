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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
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
    public function hasShipment($shippingDetails)
    {
        $shipmentsText = $this->getElement('shipments')->getText();

        return stripos($shipmentsText, $shippingDetails) !== false;
    }

    public function specifyTrackingCode($code)
    {
        $this->getDocument()->fillField('sylius_shipment_ship_tracking', $code);
    }

    public function canShipOrder(OrderInterface $order)
    {
        return $this->getLastOrderShipmentElement($order)->hasButton('Ship');
    }

    public function shipOrder(OrderInterface $order)
    {
        $this->getLastOrderShipmentElement($order)->pressButton('Ship');
    }

    /**
     * {@inheritdoc}
     */
    public function hasPayment($paymentDetails)
    {
        $paymentsText = $this->getElement('payments')->getText();

        return stripos($paymentsText, $paymentDetails) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function canCompleteOrderLastPayment(OrderInterface $order)
    {
        return $this->getLastOrderPaymentElement($order)->hasButton('Complete');
    }

    /**
     * {@inheritdoc}
     */
    public function completeOrderLastPayment(OrderInterface $order)
    {
        $this->getLastOrderPaymentElement($order)->pressButton('Complete');
    }

    /**
     * {@inheritdoc}
     */
    public function refundOrderLastPayment(OrderInterface $order)
    {
        $this->getLastOrderPaymentElement($order)->pressButton('Refund');
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

        return trim(str_replace('Subtotal:', '', $itemsTotalElement->getText()));
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
     * {@inheritdoc}
     */
    public function hasShippingPromotion($promotionName)
    {
        return $this->getElement('promotion_shipping_discounts')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasTax($tax)
    {
        $taxesText = $this->getElement('taxes')->getText();

        return stripos($taxesText, $tax) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemCode($itemName)
    {
        return $this->getItemProperty($itemName, 'sylius-product-variant-code');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUnitPrice($itemName)
    {
        return $this->getItemProperty($itemName, 'unit-price');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountedUnitPrice($itemName)
    {
        return $this->getItemProperty($itemName, 'discounted-unit-price');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemQuantity($itemName)
    {
        return $this->getItemProperty($itemName, 'quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemSubtotal($itemName)
    {
        return $this->getItemProperty($itemName, 'subtotal');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscount($itemName)
    {
        return $this->getItemProperty($itemName, 'discount');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTax($itemName)
    {
        return $this->getItemProperty($itemName, 'tax');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTotal($itemName)
    {
        return $this->getItemProperty($itemName, 'total');
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentAmount()
    {
        $paymentsPrice = $this->getElement('payments')->find('css', '.description');

        return $paymentsPrice->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentsCount()
    {
        $payments = $this->getElement('payments')->findAll('css', '.item');

        return count($payments);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCancelButton()
    {
        return $this->getDocument()->hasButton('Cancel');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderState()
    {
        return $this->getElement('order_state')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentState()
    {
        return $this->getElement('order_payment_state')->getText();
    }

    public function cancelOrder()
    {
        $this->getDocument()->pressButton('Cancel');
    }

    public function deleteOrder()
    {
        $this->getDocument()->pressButton('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function hasNote($note)
    {
        $orderNotesElement = $this->getElement('order_notes');

        return $orderNotesElement->getText() === $note;
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
    public function getIpAddressAssigned()
    {
        return $this->getElement('ip_address')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderCurrency()
    {
        return $this->getElement('currency')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRefundButton()
    {
        return $this->getDocument()->hasButton('Refund');
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingPromotionData()
    {
        return $this->getElement('promotion_shipping_discounts')->getText();
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
            'billing_address' => '#billing-address',
            'currency' => '#sylius-order-currency',
            'customer' => '#customer',
            'ip_address' => '#ipAddress',
            'items_total' => '#items-total',
            'order_notes' => '#sylius-order-notes',
            'order_payment_state' => '#payment-state > span',
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

    /**
     * @param string $itemName
     * @param string $property
     *
     * @return string
     */
    private function getItemProperty($itemName, $property)
    {
        $rows = $this->tableAccessor->getRowsWithFields(
            $this->getElement('table'),
            ['item' => $itemName]
        );

        return $rows[0]->find('css', '.'.$property)->getText();
    }

    /**
     * @param OrderInterface $order
     *
     * @return NodeElement|null
     */
    private function getLastOrderPaymentElement(OrderInterface $order)
    {
        $payment = $order->getPayments()->last();

        $paymentStateElements = $this->getElement('payments')->findAll('css', sprintf('span.ui.label:contains(\'%s\')', ucfirst($payment->getState())));
        $paymentStateElement = end($paymentStateElements);

        return $paymentStateElement->getParent()->getParent();
    }

    /**
     * @param OrderInterface $order
     *
     * @return NodeElement|null
     */
    private function getLastOrderShipmentElement(OrderInterface $order)
    {
        $shipment = $order->getShipments()->last();

        $shipmentStateElements = $this->getElement('shipments')->findAll('css', sprintf('span.ui.label:contains(\'%s\')', ucfirst($shipment->getState())));
        $shipmentStateElement = end($shipmentStateElements);

        return $shipmentStateElement->getParent()->getParent();
    }
}
