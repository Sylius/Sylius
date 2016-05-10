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
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ShippingStep extends SymfonyPage implements ShippingStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function selectShippingMethod($shippingMethod)
    {
        $radio = $this->getDocument()->findField($shippingMethod);

        Assert::notNull($radio, sprintf('Could not find "%s" shipping method', $shippingMethod));

        $this->getDocument()->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }

    /**
     * {@inheritdoc}
     */
    public function continueCheckout()
    {
        $this->getDocument()->pressButton('Continue');
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_checkout_shipping';
    }
}
