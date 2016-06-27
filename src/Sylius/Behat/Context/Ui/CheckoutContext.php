<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\AddressingPageInterface;
use Sylius\Behat\Page\Shop\Checkout\PaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\ShippingPageInterface;
use Sylius\Behat\Page\Shop\Checkout\AddressingStepInterface;
use Sylius\Behat\Page\Shop\Checkout\FinalizeStepInterface;
use Sylius\Behat\Page\Shop\Checkout\PaymentStepInterface;
use Sylius\Behat\Page\Shop\Checkout\SecurityStepInterface;
use Sylius\Behat\Page\Shop\Checkout\ShippingStepInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CanceledPaymentPageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CheckoutContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SecurityStepInterface
     */
    private $checkoutSecurityStep;

    /**
     * @var AddressingStepInterface
     */
    private $checkoutAddressingStep;

    /**
     * @var AddressingPageInterface
     */
    private $addressingPage;

    /**
     * @var ShippingStepInterface
     */
    private $checkoutShippingStep;

    /**
     * @var PaymentStepInterface
     */
    private $checkoutPaymentStep;

    /**
     * @var PaymentPageInterface
     */
    private $paymentPage;

    /**
     * @var FinalizeStepInterface
     */
    private $checkoutFinalizeStep;

    /**
     * @var ThankYouPageInterface
     */
    private $checkoutThankYouPage;

    /**
     * @var ShippingPageInterface
     */
    private $shippingPage;

    /**
     * @var CanceledPaymentPageInterface
     */
    private $canceledPaymentPage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityStepInterface $checkoutSecurityStep
     * @param AddressingStepInterface $checkoutAddressingStep
     * @param AddressingPageInterface $addressingPage
     * @param ShippingStepInterface $checkoutShippingStep
     * @param ShippingPageInterface $shippingPage
     * @param PaymentStepInterface $checkoutPaymentStep
     * @param PaymentPageInterface $paymentPage
     * @param FinalizeStepInterface $checkoutFinalizeStep
     * @param ThankYouPageInterface $checkoutThankYouPage
     * @param CanceledPaymentPageInterface $canceledPaymentPage
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityStepInterface $checkoutSecurityStep,
        AddressingStepInterface $checkoutAddressingStep,
        AddressingPageInterface $addressingPage,
        ShippingStepInterface $checkoutShippingStep,
        ShippingPageInterface $shippingPage,
        PaymentStepInterface $checkoutPaymentStep,
        PaymentPageInterface $paymentPage,
        FinalizeStepInterface $checkoutFinalizeStep,
        ThankYouPageInterface $checkoutThankYouPage,
        CanceledPaymentPageInterface $canceledPaymentPage,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->checkoutSecurityStep = $checkoutSecurityStep;
        $this->checkoutAddressingStep = $checkoutAddressingStep;
        $this->addressingPage = $addressingPage;
        $this->checkoutShippingStep = $checkoutShippingStep;
        $this->shippingPage = $shippingPage;
        $this->checkoutPaymentStep = $checkoutPaymentStep;
        $this->paymentPage = $paymentPage;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
        $this->checkoutThankYouPage = $checkoutThankYouPage;
        $this->canceledPaymentPage = $canceledPaymentPage;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given /^I proceed without selecting shipping address$/
     */
    public function iProceedWithoutSelectingShippingAddress()
    {
        $this->checkoutAddressingStep->open();
        $this->checkoutAddressingStep->continueCheckout();
    }

    /**
     * @Given I am at the checkout addressing step
     */
    public function iAmAtTheCheckoutAddressingStep()
    {
        $this->addressingPage->open();
    }

    /**
     * @When /^I specify the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I (do not specify any shipping address) information$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address)
    {
        $this->addressingPage->specifyShippingAddress($address);
    }

    /**
     * @When /^I specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I (do not specify any billing address) information$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address)
    {
        $this->addressingPage->specifyBillingAddress($address);
    }

    /**
     * @When /^I specified the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifiedTheShippingAddress(AddressInterface $address)
    {
        $this->addressingPage->open();
        $this->addressingPage->specifyShippingAddress($address);
        $this->iCompleteTheAddressingStep();
    }

    /**
     * @When I choose the different billing address
     */
    public function iChooseTheDifferentBillingAddress()
    {
        $this->addressingPage->chooseDifferentBillingAddress();
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $this->addressingPage->specifyEmail($email);
    }

    /**
     * @When I select :shippingMethod shipping method
     */
    public function iSelectShippingMethod($shippingMethod)
    {
        $this->shippingPage->selectShippingMethod($shippingMethod);
    }

    /**
     * @Then I should not be able to select :shippingMethod shipping method
     */
    public function iShouldNotBeAbleToSelectShippingMethod($shippingMethod)
    {
        Assert::false(
            $this->shippingPage->hasShippingMethod($shippingMethod),
            sprintf('Shipping method "%s" should not be available but it does.', $shippingMethod)
        );
    }

    /**
     * @When I complete the addressing step
     * @When I try to complete the addressing step
     */
    public function iCompleteTheAddressingStep()
    {
        $this->addressingPage->nextStep();
    }

    /**
     * @When I complete the shipping step
     */
    public function iCompleteTheShippingStep()
    {
        $this->shippingPage->nextStep();
    }

    /**
     * @When /^I proceed selecting "([^"]*)" as shipping country$/
     */
    public function iProceedSelectingShippingCountry($shippingCountry)
    {
        $this->checkoutAddressingStep->open();
        $this->checkoutAddressingStep->fillAddressingDetails([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'country' => $shippingCountry ?: 'France',
            'street' => '0635 Myron Hollow Apt. 711',
            'city' => 'North Bridget',
            'postcode' => '93-554',
            'phoneNumber' => '321123456',
        ]);
        $this->checkoutAddressingStep->continueCheckout();
    }

    /**
     * @When /^I proceed selecting "([^"]*)" as shipping country with "([^"]*)" method$/
     */
    public function iProceedSelectingShippingCountryAndShippingMethod($shippingCountry, $shippingMethodName)
    {
        $this->iProceedSelectingShippingCountry($shippingCountry);

        $this->checkoutShippingStep->selectShippingMethod($shippingMethodName ?: 'Free');
        $this->checkoutShippingStep->continueCheckout();
    }

    /**
     * @When /^I proceed selecting "([^"]+)" shipping method$/
     * @Given /^I chose "([^"]*)" shipping method$/
     */
    public function iProceedSelectingShippingMethod($shippingMethodName)
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod(null, $shippingMethodName);
    }

    /**
     * @When /^I choose "([^"]*)" payment method$/
     */
    public function iChoosePaymentMethod($paymentMethodName)
    {
        $this->checkoutPaymentStep->verify([]);
        $this->checkoutPaymentStep->selectPaymentMethod($paymentMethodName ?: 'Offline');
        $this->checkoutPaymentStep->continueCheckout();
    }

    /**
     * @When /^I proceed selecting "([^"]*)" as shipping country with "([^"]*)" payment method$/
     */
    public function iProceedSelectingShippingCountryAndPaymentMethod($shippingCountry, $paymentMethodName)
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod($shippingCountry, null);

        $this->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingOfflinePaymentMethod($paymentMethodName)
    {
        $this->iProceedSelectingShippingCountryAndPaymentMethod(null, $paymentMethodName);
    }

    /**
     * @When /^I change shipping method to "([^"]*)"$/
     */
    public function iChangeShippingMethod($shippingMethodName)
    {
        $this->checkoutShippingStep->open();
        $this->checkoutShippingStep->selectShippingMethod($shippingMethodName);
        $this->checkoutShippingStep->continueCheckout();
    }

    /**
     * @Given /^I proceed logging as "([^"]*)" with "([^"]*)" password$/
     */
    public function iProceedLoggingAs($login, $password)
    {
        $this->checkoutSecurityStep->open();
        $this->checkoutSecurityStep->logInAsExistingUser($login, $password);

        $this->checkoutAddressingStep->continueCheckout();
    }

    /**
     * @When /^I proceed as guest "([^"]*)" with "([^"]*)" as shipping country$/
     */
    public function iProceedLoggingAsGuestWithAsShippingCountry($email, $shippingCountry)
    {
        $this->checkoutSecurityStep->open();
        $this->checkoutSecurityStep->proceedAsGuest($email);

        $this->iProceedSelectingShippingCountry($shippingCountry);
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->checkoutFinalizeStep->confirmOrder();
    }

    /**
     * @When I specify the password as :password
     */
    public function iSpecifyThePasswordAs($password)
    {
        $this->addressingPage->specifyPassword($password);
    }

    /**
     * @When I sign in
     */
    public function iSignIn()
    {
        $this->addressingPage->signIn();
    }

    /**
     * @Then I should see the thank you page
     */
    public function iShouldSeeTheThankYouPage()
    {
        /** @var UserInterface $user */
        $user = $this->sharedStorage->get('user');
        $customer = $user->getCustomer();

        expect($this->checkoutThankYouPage->hasThankYouMessageFor($customer->getFullName()))->toBe(true);
    }

    /**
     * @Then I should be redirected back to the thank you page
     */
    public function iShouldBeRedirectedBackToTheThankYouPage()
    {
        $this->checkoutThankYouPage->waitForResponse(5);

        expect($this->checkoutThankYouPage->isOpen())->toBe(true);
    }

    /**
     * @Then I should be redirected back to the canceled payment page
     */
    public function iShouldBeRedirectedBackToTheCanceledPaymentPage()
    {
        $this->canceledPaymentPage->waitForResponse(5);

        expect($this->canceledPaymentPage->isOpen())->toBe(true);
    }

    /**
     * @Then I should be on the checkout shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep()
    {
        Assert::true(
            $this->shippingPage->isOpen(),
            'Checkout shipping page should be opened, but it is not.'
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
     * @Then I should be informed that my order cannot be shipped to this address
     */
    public function iShouldBeInformedThatMyOrderCannotBeShippedToThisAddress()
    {
        Assert::true(
            $this->shippingPage->hasNoShippingMethodsMessage(),
            'Shipping page should have no shipping methods message but it does not.'
        );
    }

    /**
     * @Then I should be able to log in
     */
    public function iShouldBeAbleToLogIn()
    {
        Assert::true(
            $this->addressingPage->canSignIn(),
            'I should be able to login, but I am not.'
        );
    }

    /**
     * @Then the login form should no longer be accessible
     */
    public function theLoginFormShouldNoLongerBeAccessible()
    {
        Assert::false(
            $this->addressingPage->canSignIn(),
            'I should not be able to login, but I am.'
        );
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true(
            $this->addressingPage->checkInvalidCredentialsValidation(),
            'I should see validation error, but I do not.'
        );
    }

    /**
     * @Given I am at the checkout payment step
     */
    public function iAmAtTheCheckoutPaymentStep()
    {
        $this->paymentPage->open();
    }

    /**
     * @When I complete the payment step
     */
    public function iCompleteThePaymentStep()
    {
        $this->paymentPage->nextStep();
    }

    /**
     * @When I select :paymentMethodName payment method
     */
    public function iSelectPaymentMethod($paymentMethodName)
    {
        $this->paymentPage->selectPaymentMethod($paymentMethodName);
    }

    /**
     * @Then I should not be able to select :paymentMethodName payment method
     */
    public function iShouldNotBeAbleToSelectPaymentMethod($paymentMethodName)
    {
        Assert::false(
            $this->paymentPage->hasPaymentMethod($paymentMethodName),
            sprintf('Payment method "%s" should not be available but it does.', $paymentMethodName)
        );
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
            $this->addressingPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('The %s should be required.', $element)
        );
    }
}
