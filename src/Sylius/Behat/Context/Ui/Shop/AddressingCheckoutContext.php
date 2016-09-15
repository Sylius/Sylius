<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddressingCheckoutContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var AddressPageInterface
     */
    private $addressPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param AddressPageInterface $addressPage
     */
    public function __construct(SharedStorageInterface $sharedStorage, AddressPageInterface $addressPage)
    {
        $this->sharedStorage = $sharedStorage;
        $this->addressPage = $addressPage;
    }

    /**
     * @Given /^I proceed without selecting shipping address$/
     */
    public function iProceedWithoutSelectingShippingAddress()
    {
        $this->addressPage->open();
        $this->addressPage->nextStep();
    }

    /**
     * @Given I am at the checkout addressing step
     * @When I go back to addressing step of the checkout
     */
    public function iAmAtTheCheckoutAddressingStep()
    {
        $this->addressPage->open();
    }

    /**
     * @When /^I specify the shipping (address as "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+")$/
     * @When /^I (do not specify any shipping address) information$/
     * @When /^I change the shipping (address to "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+")$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address)
    {
        $key = sprintf(
            'shipping_address_%s_%s',
            strtolower($address->getFirstName()),
            strtolower($address->getLastName())
        );
        $this->sharedStorage->set($key, $address);

        $this->addressPage->specifyShippingAddress($address);
    }

    /**
     * @When I specify shipping country province as :province
     */
    public function iSpecifyShippingCountryProvinceAs($province)
    {
        $this->addressPage->specifyShippingAddressProvince($province);
    }

    /**
     * @When I specify billing country province as :province
     */
    public function iSpecifyBillingCountryProvinceAs($province)
    {
        $this->addressPage->specifyBillingAddressProvince($province);
    }

    /**
     * @When /^I specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I (do not specify any billing address) information$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address)
    {
        $this->iChooseTheDifferentBillingAddress();
        $key = sprintf(
            'billing_address_%s_%s',
            strtolower($address->getFirstName()),
            strtolower($address->getLastName())
        );
        $this->sharedStorage->set($key, $address);

        $this->addressPage->specifyBillingAddress($address);
    }

    /**
     * @When /^I specified the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifiedTheShippingAddress(AddressInterface $address)
    {
        $this->addressPage->open();
        $this->iSpecifyTheShippingAddressAs($address);

        $key = sprintf('billing_address_%s_%s', strtolower($address->getFirstName()), strtolower($address->getLastName()));
        $this->sharedStorage->set($key, $address);

        $this->iCompleteTheAddressingStep();
    }

    /**
     * @When I choose the different billing address
     */
    public function iChooseTheDifferentBillingAddress()
    {
        $this->addressPage->chooseDifferentBillingAddress();
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $this->addressPage->specifyEmail($email);
    }

    /**
     * @When I complete the addressing step
     * @When I try to complete the addressing step
     */
    public function iCompleteTheAddressingStep()
    {
        $this->addressPage->nextStep();
    }

    /**
     * @When I go back to store
     */
    public function iGoBackToStore()
    {
        $this->addressPage->backToStore();
    }

    /**
     * @When /^I proceed selecting ("[^"]+" as shipping country)$/
     */
    public function iProceedSelectingShippingCountry(CountryInterface $shippingCountry = null)
    {
        $this->addressPage->open();
        $shippingAddress = $this->createDefaultAddress();
        if (null !== $shippingCountry) {
            $shippingAddress->setCountryCode($shippingCountry->getCode());
        }

        $this->addressPage->specifyShippingAddress($shippingAddress);
        $this->addressPage->nextStep();
    }

    /**
     * @When /^I proceed as guest "([^"]*)" with ("[^"]+" as shipping country)$/
     */
    public function iProceedLoggingAsGuestWithAsShippingCountry($email, CountryInterface $shippingCountry = null)
    {
        $this->addressPage->open();
        $this->addressPage->specifyEmail($email);
        $shippingAddress = $this->createDefaultAddress();
        if (null !== $shippingCountry) {
            $shippingAddress->setCountryCode($shippingCountry->getCode());
        }

        $this->addressPage->specifyShippingAddress($shippingAddress);
        $this->addressPage->nextStep();
    }

    /**
     * @When I specify the password as :password
     */
    public function iSpecifyThePasswordAs($password)
    {
        $this->addressPage->specifyPassword($password);
    }

    /**
     * @When I sign in
     */
    public function iSignIn()
    {
        $this->addressPage->signIn();
    }

    /**
     * @Then I should be able to log in
     */
    public function iShouldBeAbleToLogIn()
    {
        Assert::true(
            $this->addressPage->canSignIn(),
            'I should be able to login, but I am not.'
        );
    }

    /**
     * @Then the login form should no longer be accessible
     */
    public function theLoginFormShouldNoLongerBeAccessible()
    {
        Assert::false(
            $this->addressPage->canSignIn(),
            'I should not be able to login, but I am.'
        );
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true(
            $this->addressPage->checkInvalidCredentialsValidation(),
            'I should see validation error, but I do not.'
        );
    }

    /**
     * @Then I should be redirected to the addressing step
     */
    public function iShouldBeRedirectedToTheAddressingStep()
    {
        Assert::true(
            $this->addressPage->isOpen(),
            'Checkout addressing step should be opened, but it is not.'
        );
    }

    /**
     * @When /^I complete addressing step with email "([^"]+)" and ("([^"]+)" as shipping country)$/
     */
    public function iCompleteAddressingStepWithEmail($email, AddressInterface $address)
    {
        $this->addressPage->open();
        $this->iSpecifyTheEmail($email);
        $this->iSpecifyTheShippingAddressAs($address);
        $this->iCompleteTheAddressingStep();
    }

    /**
     * @Then I should be on the checkout addressing step
     */
    public function iShouldBeOnTheCheckoutAddressingStep()
    {
        Assert::true(
            $this->addressPage->isOpen(),
            'Checkout addressing page should be opened, but it is not.'
        );
    }

    /**
     * @Then /^I should(?:| also) be notified that the "([^"]+)" and the "([^"]+)" in (shipping|billing) details are required$/
     */
    public function iShouldBeNotifiedThatTheAndTheInShippingDetailsAreRequired($firstElement, $secondElement, $type)
    {
        $this->assertElementValidationMessage($type, $firstElement, sprintf('Please enter %s.', $firstElement));
        $this->assertElementValidationMessage($type, $secondElement, sprintf('Please enter %s.', $secondElement));
    }

    /**
     * @return AddressInterface
     */
    private function createDefaultAddress()
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCountryCode('US');
        $address->setCity('North Bridget');
        $address->setPostcode('93-554');
        $address->setStreet('0635 Myron Hollow Apt. 711');
        $address->setPhoneNumber('321123456');

        return $address;
    }

    /**
     * @param string $type
     * @param string $element
     * @param string $expectedMessage
     *
     * @throws \InvalidArgumentException
     */
    private function assertElementValidationMessage($type, $element, $expectedMessage)
    {
        $element = sprintf('%s_%s', $type, implode('_', explode(' ', $element)));
        Assert::true(
            $this->addressPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('The %s should be required.', $element)
        );
    }
}
