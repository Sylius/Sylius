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
interface AddressPageInterface extends SymfonyPageInterface
{
    /**
     * @throws \RuntimeException
     */
    public function chooseDifferentBillingAddress();

    /**
     * @return bool
     */
    public function checkInvalidCredentialsValidation();

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor($element, $message);

    /**
     * @param AddressInterface $billingAddress
     */
    public function specifyBillingAddress(AddressInterface $billingAddress);

    /**
     * @param string $province
     */
    public function selectBillingAddressProvince($province);

    /**
     * @param AddressInterface $shippingAddress
     */
    public function specifyShippingAddress(AddressInterface $shippingAddress);

    /**
     * @param string $province
     */
    public function selectShippingAddressProvince($province);

    /**
     * @return bool
     */
    public function canSignIn();

    public function signIn();

    /**
     * @param string $email
     */
    public function specifyEmail($email);

    /**
     * @param string $password
     */
    public function specifyPassword($password);

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal($itemName);

    /**
     * @return string
     */
    public function getShippingAddressCountry();

    public function nextStep();

    public function backToStore();

    /**
     * @param string $provinceName
     */
    public function specifyBillingAddressProvince($provinceName);

    /**
     * @param string $provinceName
     */
    public function specifyShippingAddressProvince($provinceName);

    /**
     * @return bool
     */
    public function hasShippingAddressInput();

    /**
     * @return bool
     */
    public function hasBillingAddressInput();

    /**
     * @param AddressInterface $address
     */
    public function selectShippingAddressFromAddressBook(AddressInterface $address);

    /**
     * @param AddressInterface $address
     */
    public function selectBillingAddressFromAddressBook(AddressInterface $address);

    /**
     * @return AddressInterface
     */
    public function getPreFilledShippingAddress();

    /**
     * @return AddressInterface
     */
    public function getPreFilledBillingAddress();
}
