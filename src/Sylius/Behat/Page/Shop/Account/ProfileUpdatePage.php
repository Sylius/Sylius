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
use Sylius\Behat\Page\SymfonyPage;

class ProfileUpdatePage extends SymfonyPage implements ProfileUpdatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_account_profile_update';
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidationMessageFor($element, $message)
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '.sylius-validation-error');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $message === $errorLabel->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyFirstName($firstName)
    {
        $this->getDocument()->fillField('First name', $firstName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyLastName($lastName)
    {
        $this->getDocument()->fillField('Last name', $lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyEmail($email)
    {
        $this->getDocument()->fillField('Email', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCustomerAddressName(string $firstName, string $lastName): void
    {
        $this->getDocument()->fillField('sylius_customer_defaultAddress_firstName', $firstName);
        $this->getDocument()->fillField('sylius_customer_defaultAddress_lastName', $lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCustomerAddressPhone(string $phoneNumber): void
    {
        $this->getDocument()->fillField('sylius_customer_defaultAddress_phoneNumber', $phoneNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCustomerAddressCompany(string $company): void
    {
        $this->getDocument()->fillField('sylius_customer_defaultAddress_company', $company);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCustomerAddressCountry(string $country): void
    {
        $this->getDocument()->fillField('sylius_customer_defaultAddress_countryCode', $country);
    }

    /**
     * {@inheritdocs}
     */
    public function specifyCustomerAddressStreets(string $street, string $city, string $postCode, string $province): void
    {
        $this->getDocument()->fillField('sylius_customer_defaultAddress_street', $street);
        $this->getDocument()->fillField('sylius_customer_defaultAddress_city', $city);
        $this->getDocument()->fillField('sylius_customer_defaultAddress_postcode', $postCode);
        $this->getDocument()->fillField('sylius_customer_defaultAddress_provinceName', $province);
    }

    /**
     * {@inheritdoc}
     */
    public function saveChanges()
    {
        $this->getDocument()->pressButton('Save changes');
    }

    public function subscribeToTheNewsletter()
    {
        $this->getDocument()->checkField('Subscribe to the newsletter');
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedToTheNewsletter()
    {
        return $this->getDocument()->hasCheckedField('Subscribe to the newsletter');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '#sylius_customer_profile_email',
            'first_name' => '#sylius_customer_profile_firstName',
            'last_name' => '#sylius_customer_profile_lastName',
        ]);
    }
}
