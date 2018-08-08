<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

interface ProfileUpdatePageInterface extends PageInterface
{
    /**
     * @param string $firstName
     */
    public function specifyFirstName($firstName);

    /**
     * @param string $lastName
     */
    public function specifyLastName($lastName);

    /**
     * @param string $email
     */
    public function specifyEmail($email);

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function specifyCustomerAddressName(string $firstName, string $lastName): void;

    /**
     * @param string $phoneNumber
     */
    public function specifyCustomerAddressPhone(string $phoneNumber): void;

    /**
     * @param string $company
     */
    public function specifyCustomerAddressCompany(string $company): void;

    /**
     * @param string $country
     */
    public function specifyCustomerAddressCountry(string $country): void;

    /**
     * @param string $street
     * @param string $city
     * @param string $postCode
     * @param string $province
     */
    public function specifyCustomerAddressStreets(string $street, string $city, string $postCode, string $province): void;

    public function saveChanges();

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor($element, $message);

    public function subscribeToTheNewsletter();

    /**
     * @return bool
     */
    public function isSubscribedToTheNewsletter();
}
