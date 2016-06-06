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

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface AddressingPageInterface extends SymfonyPageInterface
{
    /**
     * @throws \RuntimeException
     */
    public function chooseDifferentBillingAddress();

    /**
     * @param string $element
     * @param string $message
     * 
     * @return bool
     */
    public function checkValidationMessageFor($element, $message);

    /**
     * @param string|null $firstName
     */
    public function specifyShippingAddressFirstName($firstName = null);

    /**
     * @param string|null $lastName
     */
    public function specifyShippingAddressLastName($lastName = null);

    /**
     * @param string|null $streetName
     */
    public function specifyShippingAddressStreet($streetName = null);

    /**
     * @param string|null $countryName
     */
    public function chooseShippingAddressCountry($countryName = null);

    /**
     * @param string|null $cityName
     */
    public function specifyShippingAddressCity($cityName = null);

    /**
     * @param string|null $postcode
     */
    public function specifyShippingAddressPostcode($postcode = null);

    /**
     * @param AddressInterface $shippingAddress
     */
    public function specifyShippingAddress(AddressInterface $shippingAddress);

    /**
     * @param string|null $firstName
     */
    public function specifyBillingAddressFirstName($firstName = null);

    /**
     * @param string|null $lastName
     */
    public function specifyBillingAddressLastName($lastName = null);

    /**
     * @param string|null $streetName
     */
    public function specifyBillingAddressStreet($streetName = null);

    /**
     * @param string|null $countryName
     */
    public function chooseBillingAddressCountry($countryName = null);

    /**
     * @param string|null $cityName
     */
    public function specifyBillingAddressCity($cityName = null);

    /**
     * @param string|null $postcode
     */
    public function specifyBillingAddressPostcode($postcode = null);

    public function nextStep();
}
