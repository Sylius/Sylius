<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Checkout;

use Sylius\Behat\SymfonyPageObjectExtension\PageObject\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutPaymentStep extends SymfonyPage
{
    /**
     * @param string $paymentMethod
     */
    public function selectPaymentMethod($paymentMethod)
    {
        $radio = $this->getDocument()->findField($paymentMethod);

        $this->getDocument()->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }

    public function continueCheckout()
    {
        $this->getDocument()->pressButton('Continue');
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return 'sylius_checkout_payment';
    }
}
