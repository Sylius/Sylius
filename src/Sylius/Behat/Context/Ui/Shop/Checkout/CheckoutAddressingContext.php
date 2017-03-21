<?php

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CheckoutAddressingContext implements Context
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
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var AddressComparatorInterface
     */
    private $addressComparator;

    /**
     * @var SelectShippingPageInterface
     */
    private $selectShippingPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param AddressPageInterface $addressPage
     * @param FactoryInterface $addressFactory
     * @param AddressComparatorInterface $addressComparator
     * @param SelectShippingPageInterface $selectShippingPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        AddressPageInterface $addressPage,
        FactoryInterface $addressFactory,
        AddressComparatorInterface $addressComparator,
        SelectShippingPageInterface $selectShippingPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->addressPage = $addressPage;
        $this->addressFactory = $addressFactory;
        $this->addressComparator = $addressComparator;
        $this->selectShippingPage = $selectShippingPage;
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
     * @Given /^I have completed addressing step with email "([^"]+)" and ("[^"]+" based shipping address)$/
     * @When /^I complete addressing step with email "([^"]+)" and ("[^"]+" based shipping address)$/
     */
    public function iCompleteAddressingStepWithEmail($email, AddressInterface $address)
    {
        $this->addressPage->open();
        $this->iSpecifyTheEmail($email);
        $this->iSpecifyTheShippingAddressAs($address);
        $this->iCompleteTheAddressingStep();
    }

    /**
     * @When I specify the province name manually as :provinceName for shipping address
     */
    public function iSpecifyTheProvinceNameManuallyAsForShippingAddress($provinceName)
    {
        $this->addressPage->specifyShippingAddressProvince($provinceName);
    }

    /**
     * @When I specify the province name manually as :provinceName for billing address
     */
    public function iSpecifyTheProvinceNameManuallyAsForBillingAddress($provinceName)
    {
        $this->addressPage->specifyBillingAddressProvince($provinceName);
    }

    /**
     * @When I try to open checkout addressing page
     */
    public function iTryToOpenCheckoutAddressingPage()
    {
        $this->addressPage->tryToOpen();
    }

    /**
     * @When /^I choose ("[^"]+" street) for shipping address$/
     */
    public function iChooseForShippingAddress(AddressInterface $address)
    {
        $this->addressPage->selectShippingAddressFromAddressBook($address);
    }

    /**
     * @When /^I choose ("[^"]+" street) for billing address$/
     */
    public function iChooseForBillingAddress(AddressInterface $address)
    {
        $this->addressPage->chooseDifferentBillingAddress();
        $this->addressPage->selectBillingAddressFromAddressBook($address);
    }

    /**
     * @When /^I specify the shipping (address as "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+")$/
     * @When /^I specify the shipping (address for "[^"]+" from "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+")$/
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
        $this->addressPage->selectShippingAddressProvince($province);
    }

    /**
     * @When I specify billing country province as :province
     */
    public function iSpecifyBillingCountryProvinceAs($province)
    {
        $this->addressPage->selectBillingAddressProvince($province);
    }

    /**
     * @When /^I specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I specify the billing (address for "([^"]+)" from "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     * @When /^I (do not specify any billing address) information$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address)
    {
        $this->addressPage->chooseDifferentBillingAddress();

        $key = sprintf(
            'billing_address_%s_%s',
            strtolower($address->getFirstName()),
            strtolower($address->getLastName())
        );
        $this->sharedStorage->set($key, $address);

        $this->addressPage->specifyBillingAddress($address);
    }

    /**
     * @When /^I specified the shipping (address as "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+")$/
     */
    public function iSpecifiedTheShippingAddress(AddressInterface $address = null)
    {
        if (null === $address) {
            $address = $this->createDefaultAddress();
        }

        $this->addressPage->open();
        $this->iSpecifyTheShippingAddressAs($address);

        $key = sprintf('billing_address_%s_%s', strtolower($address->getFirstName()), strtolower($address->getLastName()));
        $this->sharedStorage->set($key, $address);

        $this->iCompleteTheAddressingStep();
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
    public function iProceedSelectingShippingCountry(CountryInterface $shippingCountry = null, $localeCode = 'en_US')
    {
        $this->addressPage->open(['_locale' => $localeCode]);
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
     * @Then I should have :countryName selected as country
     */
    public function iShouldHaveSelectedAsCountry($countryName)
    {
        Assert::same($this->addressPage->getShippingAddressCountry(), $countryName);
    }

    /**
     * @Then I should have no country selected
     */
    public function iShouldHaveNoCountrySelected()
    {
        Assert::same($this->addressPage->getShippingAddressCountry(), 'Select');
    }

    /**
     * @Then I should be able to log in
     */
    public function iShouldBeAbleToLogIn()
    {
        Assert::true($this->addressPage->canSignIn());
    }

    /**
     * @Then the login form should no longer be accessible
     */
    public function theLoginFormShouldNoLongerBeAccessible()
    {
        Assert::false($this->addressPage->canSignIn());
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true($this->addressPage->checkInvalidCredentialsValidation());
    }

    /**
     * @Then I should be redirected to the addressing step
     * @Then I should be on the checkout addressing step
     */
    public function iShouldBeRedirectedToTheAddressingStep()
    {
        $this->addressPage->verify();
    }

    /**
     * @Then I should be able to go to the shipping step again
     */
    public function iShouldBeAbleToGoToTheShippingStepAgain()
    {
        $this->addressPage->nextStep();

        $this->selectShippingPage->verify();
    }

    /**
     * @Then I should not be able to specify province name manually for shipping address
     */
    public function iShouldNotBeAbleToSpecifyProvinceNameManuallyForShippingAddress()
    {
        Assert::false($this->addressPage->hasShippingAddressInput());
    }

    /**
     * @Then I should not be able to specify province name manually for billing address
     */
    public function iShouldNotBeAbleToSpecifyProvinceNameManuallyForBillingAddress()
    {
        Assert::false($this->addressPage->hasBillingAddressInput());
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+") should be filled as shipping address$/
     */
    public function addressShouldBeFilledAsShippingAddress(AddressInterface $address)
    {
        Assert::true($this->addressComparator->equal($address, $this->addressPage->getPreFilledShippingAddress()));
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+") should be filled as billing address$/
     */
    public function addressShouldBeFilledAsBillingAddress(AddressInterface $address)
    {
        Assert::true($this->addressComparator->equal($address, $this->addressPage->getPreFilledBillingAddress()));
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
        Assert::true($this->addressPage->checkValidationMessageFor($element, $expectedMessage));
    }
}
