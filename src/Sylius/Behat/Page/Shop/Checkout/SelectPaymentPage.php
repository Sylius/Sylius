<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class SelectPaymentPage extends SymfonyPage implements SelectPaymentPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_select_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function selectPaymentMethod($paymentMethod)
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $paymentMethodOption = $this->getElement('payment_method_select');
            $paymentMethodOption->getParent()->click();

            return;
        }

        $paymentMethodElement = $this->getElement('payment_method');
        $paymentMethodValue = $this->getElement('payment_method_option', ['%payment_method%' => $paymentMethod])->getAttribute('value');
        $paymentMethodElement->selectOption($paymentMethodValue);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPaymentMethod($paymentMethodName)
    {
        try {
            $this->getElement('payment_method_option', ['%payment_method%' => $paymentMethodName]);
        } catch (ElementNotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemSubtotal($itemName)
    {
        $itemSlug = strtolower(str_replace('\"', '', str_replace(' ', '-', $itemName)));

        $subtotalTable = $this->getElement('checkout_subtotal');

        return $subtotalTable->find('css', sprintf('#item-%s-subtotal', $itemSlug))->getText();
    }

    public function nextStep()
    {
        $this->getElement('next_step')->press();
    }

    public function changeShippingMethod()
    {
        $this->getDocument()->clickLink('Change shipping method');
    }

    public function changeShippingMethodByStepLabel()
    {
        $this->getElement('shipping_step_label')->click();
    }

    public function changeAddressByStepLabel()
    {
        $this->getElement('address_step_label')->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'address_step_label' => '.steps a:contains("Address")',
            'checkout_subtotal' => '#checkout-subtotal',
            'next_step' => '#next-step',
            'order_cannot_be_paid_message' => '#sylius-order-cannot-be-paid',
            'payment_method' => '[name="sylius_checkout_select_payment[payments][0][method]"]',
            'payment_method_option' => '.item:contains("%payment_method%") input',
            'payment_method_select' => '#sylius_checkout_select_payment_payments_0_method_0',
            'shipping_step_label' => '.steps a:contains("Shipping")',
        ]);
    }
}
