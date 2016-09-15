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
        $shippingMethodElement = $this->getElement('shipping_method');
        $shippingMethodValue = $this->getElement('shipping_method_option', ['%shipping_method%' => $shippingMethod])->getAttribute('value');

        $shippingMethodElement->selectOption($shippingMethodValue);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingMethod($shippingMethod)
    {
        try {
            $this->getElement('shipping_method_option', ['%shipping_method%' => $shippingMethod]);
        } catch (ElementNotFoundException $exception) {
            return false;
        }

        return true;
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

        return $subtotalTable->find('css', sprintf('#item-%s-subtotal', $itemSlug))->getText();
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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'address' => '.steps a:contains("Address")',
            'checkout_subtotal' => '#checkout-subtotal',
            'next_step' => '#next-step',
            'order_cannot_be_shipped_message' => '#sylius-order-cannot-be-shipped',
            'shipping_method' => '[name="sylius_checkout_select_shipping[shipments][0][method]"]',
            'shipping_method_option' => '.item:contains("%shipping_method%") input',
            'shipping_method_fee' => '.item:contains("%shipping_method%") .fee',
        ]);
    }
}
