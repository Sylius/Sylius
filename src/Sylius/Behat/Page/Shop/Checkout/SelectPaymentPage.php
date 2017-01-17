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
            $this->getElement('payment_method_select', ['%payment_method%' => $paymentMethod])->click();

            return;
        }

        $paymentMethodOptionElement = $this->getElement('payment_method_option', ['%payment_method%' => $paymentMethod]);
        $paymentMethodOptionElement->selectOption($paymentMethodOptionElement->getAttribute('value'));
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

        return $subtotalTable->find('css', sprintf('#sylius-item-%s-subtotal', $itemSlug))->getText();
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
    public function hasNoAvailablePaymentMethodsWarning()
    {
        return $this->hasElement('warning_no_payment_methods');
    }

    /**
     * {@inheritdoc}
     */
     public function isNextStepButtonUnavailable()
     {
         return $this->getElement('next_step')->hasClass('disabled');
     }

    /**
     * {@inheritdoc}
     */
     public function getPaymentMethods()
     {
         $inputs = $this->getSession()->getPage()->findAll('css', '#sylius-payment-methods .item .content label');

         $paymentMethods = [];
         foreach ($inputs as $input) {
             $paymentMethods[] = trim($input->getText());
         }

         return $paymentMethods;
     }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'address_step_label' => '.steps a:contains("Address")',
            'checkout_subtotal' => '#sylius-checkout-subtotal',
            'next_step' => '#next-step',
            'order_cannot_be_paid_message' => '#sylius-order-cannot-be-paid',
            'payment_method_option' => '.item:contains("%payment_method%") input',
            'payment_method_select' => '.item:contains("%payment_method%") > .field > .ui.radio.checkbox',
            'shipping_step_label' => '.steps a:contains("Shipping")',
            'warning_no_payment_methods' => '#sylius-order-cannot-be-paid',
        ]);
    }
}
