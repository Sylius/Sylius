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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PaymentPage extends SymfonyPage implements PaymentPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function selectPaymentMethod($paymentMethod)
    {
        $paymentMethodElement = $this->getElement('payment_method');
        $paymentMethodElement->selectOption($paymentMethodElement->getAttribute('value'));
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

    public function nextStep()
    {
        $this->getDocument()->pressButton('Next');
    }

    public function changeShippingMethod()
    {
        $this->getDocument()->pressButton('Change shipping method');
    }

    public function changeShippingMethodByStepLabel()
    {
        $this->getElement('shipping_step_label')->click();
    }

    public function changeAddressByStepLabel()
    {
        $this->getElement('addressing_step_label')->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'addressing_step_label' => '.steps a:contains("Addressing")',
            'order_cannot_be_paid_message' => '#sylius-order-cannot-be-paid',
            'payment_method' => '[name="sylius_checkout_payment_step[payments][0][method]"]',
            'payment_method_option' => '.item:contains("%payment_method%") input',
            'shipping_step_label' => '.steps a:contains("Shipping")',
        ]);
    }
}
