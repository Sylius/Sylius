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

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutPaymentStep extends SymfonyPage implements CheckoutPaymentStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function selectPaymentMethod($paymentMethod)
    {
        $radio = $this->getDocument()->findField($paymentMethod);

        if (null === $radio) {
            throw new ElementNotFoundException('Payment method not found or it is not visible');
        }

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
    protected function getRouteName()
    {
        return 'sylius_checkout_payment';
    }
}
