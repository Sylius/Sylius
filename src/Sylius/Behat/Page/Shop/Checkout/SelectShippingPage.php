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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class SelectShippingPage extends SymfonyPage implements SelectShippingPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_select_shipping';
    }

    /**
     * {@inheritdoc}
     */
    public function selectShippingMethod($shippingMethod)
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getElement('shipping_method_select', ['%shipping_method%' => $shippingMethod])->click();

            return;
        }

        $shippingMethodOptionElement = $this->getElement('shipping_method_option', ['%shipping_method%' => $shippingMethod]);
        $shippingMethodOptionElement->selectOption($shippingMethodOptionElement->getAttribute('value'));
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethods()
    {
        $inputs = $this->getSession()->getPage()->findAll('css', '#sylius-shipping-methods .item .content label');

        $shippingMethods = [];
        foreach ($inputs as $input) {
            $shippingMethods[] = trim($input->getText());
        }

        return $shippingMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNoShippingMethodsMessage()
    {
        try {
            $this->getElement('order_cannot_be_shipped_message');
        } catch (ElementNotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingMethodFee($shippingMethodName, $fee)
    {
        $feeElement = $this->getElement('shipping_method_fee', ['%shipping_method%' => $shippingMethodName])->getText();

        return false !== strpos($feeElement, $fee);
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

    public function changeAddress()
    {
        $this->getDocument()->clickLink('Change address');
    }

    public function changeAddressByStepLabel()
    {
        $this->getElement('address')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchaserEmail()
    {
        return $this->getElement('purchaser-email')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForShipment()
    {
        $foundElement = $this->getElement('shipment');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Items element');
        }

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNoAvailableShippingMethodsWarning()
    {
        return $this->hasElement('warning_no_shipping_methods');
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
    public function hasShippingMethod($shippingMethodName)
    {
        $inputs = $this->getSession()->getPage()->findAll('css', '#sylius-shipping-methods .item .content label');

        $shippingMethods = [];
        foreach ($inputs as $input) {
            $shippingMethods[] = trim($input->getText());
        }

        return in_array($shippingMethodName, $shippingMethods);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'address' => '.steps a:contains("Address")',
            'checkout_subtotal' => '#sylius-checkout-subtotal',
            'next_step' => '#next-step',
            'order_cannot_be_shipped_message' => '#sylius-order-cannot-be-shipped',
            'purchaser-email' => '#purchaser-email',
            'shipment' => '.items',
            'shipping_method' => '[name="sylius_checkout_select_shipping[shipments][0][method]"]',
            'shipping_method_fee' => '.item:contains("%shipping_method%") .fee',
            'shipping_method_select' => '.item:contains("%shipping_method%") > .field > .ui.radio.checkbox',
            'shipping_method_option' => '.item:contains("%shipping_method%") input',
            'warning_no_shipping_methods' => '#sylius-order-cannot-be-shipped'
        ]);
    }
}
