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

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderDetailsPage extends SymfonyPage implements OrderDetailsPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasPayAction()
    {
        return $this->hasElement('pay_link');
    }

    /**
     * {@inheritdoc}
     */
    public function pay()
    {
        $this->getElement('pay_link')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function choosePaymentMethod($paymentMethodName)
    {
        $paymentMethodElement = $this->getElement('payment_method', ['%name%' => $paymentMethodName]);
        $paymentMethodElement->selectOption($paymentMethodElement->getAttribute('value'));
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_order_show_details';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'instructions' => '#sylius-payment-method-instructions',
            'pay_link' => '#sylius-pay-link',
            'payment_method' => '.item:contains("%name%") input',
            'thank_you' => '#sylius-thank-you',
        ]);
    }
}
