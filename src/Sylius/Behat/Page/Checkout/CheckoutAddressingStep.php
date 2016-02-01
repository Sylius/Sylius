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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\PageObjectExtension\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutAddressingStep extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_checkout_addressing';
    }

    /**
     * @param array $addressingDetails
     *
     * @throws ElementNotFoundException
     */
    public function fillAddressingDetails(array $addressingDetails)
    {
        $this->fillField('First name', $addressingDetails['firstName']);
        $this->fillField('Last name', $addressingDetails['lastName']);
        $this->selectFieldOption('Country', $addressingDetails['country']);
        $this->fillField('Street', $addressingDetails['street']);
        $this->fillField('City', $addressingDetails['city']);
        $this->fillField('Postcode', $addressingDetails['postcode']);
        $this->fillField('Phone number', $addressingDetails['phoneNumber']);
    }

    public function forward()
    {
        $this->pressButton('Continue');
    }
}
