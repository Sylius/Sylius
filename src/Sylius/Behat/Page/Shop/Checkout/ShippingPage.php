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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingPage extends SymfonyPage implements ShippingPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_shipping';
    }

    /**
     * {@inheritdoc}
     */
    public function selectShippingMethod($shippingMethod)
    {
        $shippingMethodElement = $this->getElement('shipping_method', ['%shipping_method%' => $shippingMethod]);
        $shippingMethodElement->check();
    }

    public function nextStep()
    {
        $this->getDocument()->pressButton('Next');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'shipping_method' => '#shipping_methods .content:contains("%shipping_method%") ~ .field input',
        ]);
    }
}
