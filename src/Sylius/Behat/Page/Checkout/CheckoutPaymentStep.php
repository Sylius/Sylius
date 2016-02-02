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

use Sylius\Behat\PageObjectExtension\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutPaymentStep extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_checkout_payment';
    }

    /**
     * @param string $paymentMethod
     */
    public function selectPaymentMethod($paymentMethod)
    {
        $this->pressRadio($paymentMethod);
        $this->pressButton('Continue');
    }
}
