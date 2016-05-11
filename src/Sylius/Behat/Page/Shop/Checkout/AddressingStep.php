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
class AddressingStep extends SymfonyPage implements AddressingStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function fillAddressingDetails(array $addressingDetails)
    {
        $document = $this->getDocument();

        $document->fillField('First name', $addressingDetails['firstName']);
        $document->fillField('Last name', $addressingDetails['lastName']);
        $document->selectFieldOption('Country', $addressingDetails['country']);
        $document->fillField('Street', $addressingDetails['street']);
        $document->fillField('City', $addressingDetails['city']);
        $document->fillField('Postcode', $addressingDetails['postcode']);
        $document->fillField('Phone number', $addressingDetails['phoneNumber']);
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
        return 'sylius_checkout_addressing';
    }
}
