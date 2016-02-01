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
class CheckoutShippingStep extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_checkout_shipping';
    }

    /**
     * @param string $shippingMethod
     */
    public function selectShippingMethod($shippingMethod)
    {
        $this->pressRadio($shippingMethod);
        $this->pressButton('Continue');
    }
}
