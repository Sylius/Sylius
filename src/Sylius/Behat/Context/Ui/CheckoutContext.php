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
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Checkout\AddressingPageInterface;
use Sylius\Behat\Page\Shop\Order\OrderPaymentsPageInterface;
use Sylius\Behat\Page\Shop\Checkout\AddressingStepInterface;
use Sylius\Behat\Page\Shop\Checkout\FinalizeStepInterface;
use Sylius\Behat\Page\Shop\Checkout\PaymentStepInterface;
use Sylius\Behat\Page\Shop\Checkout\SecurityStepInterface;
use Sylius\Behat\Page\Shop\Checkout\ShippingStepInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
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
     * @var FinalizeStepInterface
     */
    private $checkoutFinalizeStep;

    /**
     * @var ThankYouPageInterface
     */
    private $checkoutThankYouPage;

    /**
     * @var OrderPaymentsPageInterface
     */
    private $orderPaymentsPage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityStepInterface $checkoutSecurityStep
     * @param AddressingStepInterface $checkoutAddressingStep
     * @param AddressingPageInterface $addressingPage
     * @param ShippingStepInterface $checkoutShippingStep
     * @param PaymentStepInterface $checkoutPaymentStep
     * @param FinalizeStepInterface $checkoutFinalizeStep
     * @param ThankYouPageInterface $checkoutThankYouPage
     * @param OrderPaymentsPageInterface $orderPaymentsPage
     * @param OrderRepositoryInterface $orderRepository
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityStepInterface $checkoutSecurityStep,
        AddressingStepInterface $checkoutAddressingStep,
        AddressingPageInterface $addressingPage,
        ShippingStepInterface $checkoutShippingStep,
        PaymentStepInterface $checkoutPaymentStep,
        FinalizeStepInterface $checkoutFinalizeStep,
        ThankYouPageInterface $checkoutThankYouPage,
        OrderPaymentsPageInterface $orderPaymentsPage,
        OrderRepositoryInterface $orderRepository,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->checkoutSecurityStep = $checkoutSecurityStep;
        $this->checkoutAddressingStep = $checkoutAddressingStep;
        $this->addressingPage = $addressingPage;
        $this->checkoutShippingStep = $checkoutShippingStep;
        $this->checkoutPaymentStep = $checkoutPaymentStep;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
        $this->checkoutThankYouPage = $checkoutThankYouPage;
        $this->orderPaymentsPage = $orderPaymentsPage;
        $this->orderRepository = $orderRepository;
        $this->notificationChecker = $notificationChecker;
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
     * @Given I proceed with the checkout addressing step
     */
    public function iProceedWithTheCheckoutAddressingStep()
    {
        $this->addressingPage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I do not specify the first name
     */
    public function iSpecifyTheFirstName($firstName = null)
    {
        $this->addressingPage->specifyShippingAddressFirstName($firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I do not specify the last name
     */
    public function iSpecifyTheLastName($lastName = null)
    {
        $this->addressingPage->specifyShippingAddressLastName($lastName);
    }

    /**
     * @When I specify the street as :streetName
     * @When I do not specify the street
     */
    public function iSpecifyTheStreetAs($streetName = null)
    {
        $this->addressingPage->specifyShippingAddressStreet($streetName);
    }

    /**
     * @When I choose :countryName
     * @When I do not choose the country
     */
    public function iChoose($countryName = null)
    {
        $this->addressingPage->chooseShippingAddressCountry($countryName);
    }

    /**
     * @When I specify the city as :cityName
     * @When I do not specify the city
     */
    public function iSpecifyTheCityAs($cityName = null)
    {
        $this->addressingPage->specifyShippingAddressCity($cityName);
    }

    /**
     * @When I specify the postcode as :postcode
     * @When I do not specify the postcode
     */
    public function iSpecifyThePostcodeAs($postcode = null)
    {
        $this->addressingPage->specifyShippingAddressPostcode($postcode);
    }

    /**
     * @When /^I specify the shipping (address)$/
     */
    public function iSpecifyTheShippingAddress(AddressInterface $address)
    {
        $this->addressingPage->specifyShippingAddress($address);
    }

    /**
     * @When I choose the different billing address
     */
    public function iChooseTheDifferentBillingAddress()
    {
        $this->addressingPage->chooseDifferentBillingAddress();
    }

    /**
     * @When I specify the billing's first name as :firstName
     * @When I do not specify the billing's first name
     */
    public function iSpecifyTheBillingSFirstNameAs($firstName = null)
    {
        $this->addressingPage->specifyBillingAddressFirstName($firstName);
    }

    /**
     * @When I specify the billing's last name as :lastName
     * @When I do not specify the billing's last name
     */
    public function iSpecifyTheBillingSLastNameAs($lastName = null)
    {
        $this->addressingPage->specifyBillingAddressLastName($lastName);
    }

    /**
     * @When I specify the billing's street as :streetName
     * @When I do not specify the billing's street
     */
    public function iSpecifyTheBillingSStreetAs($streetName = null)
    {
        $this->addressingPage->specifyBillingAddressStreet($streetName);
    }

    /**
     * @Given I choose :countryName as billing's country
     */
    public function iChooseAsBillingSCountry($countryName)
    {
        $this->addressingPage->chooseBillingAddressCountry($countryName);
    }

    /**
     * @When I specify the billing's city as :cityName
     * @When I do not specify the billing's city
     */
    public function iSpecifyTheBillingSCityAs($cityName = null)
    {
        $this->addressingPage->specifyBillingAddressCity($cityName);
    }

    /**
     * @When I specify the billing's postcode as :postcode
     * @When I do not specify the billing's postcode
     */
    public function iSpecifyTheBillingSPostcodeAs($postcode = null)
    {
        $this->addressingPage->specifyBillingAddressPostcode($postcode);
    }

    /**
     * @When I proceed with the next step
     * @When I try to proceed with the next step
     */
    public function iProceedWithTheNextStep()
    {
        $this->addressingPage->nextStep();
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
     * @Then I should be redirected back to the order payment page
     */
    public function iShouldBeRedirectedBackToTheOrderPaymentPage()
    {
        $this->orderPaymentsPage->waitForResponse(5, ['number' => $this->getLastOrder()->getNumber()]);

        expect($this->orderPaymentsPage->isOpen(['number' => $this->getLastOrder()->getNumber()]))->toBe(true);
    }

    /**
     * @Then I should see two cancelled payments and new one ready to be paid
     */
    public function iShouldSeeTwoCancelledPaymentsAndNewOneReadyToBePaid()
    {
        expect($this->orderPaymentsPage->countPaymentWithSpecificState(PaymentInterface::STATE_CANCELLED))->toBe(2);
        expect($this->orderPaymentsPage->countPaymentWithSpecificState(PaymentInterface::STATE_NEW))->toBe(1);
    }

    /**
     * @Then I should be notified that the order has been successfully addressed
     */
    public function iShouldBeNotifiedThatTheOrderHasBeenSuccessfullyAddressed()
    {
        $this->notificationChecker->checkNotification('Order has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then /^I should be notified that the "([^"]+)" "([^"]+)" is required$/
     * @Then /^the "([^"]+)" "([^"]+)" is also required$/
     */
    public function iShouldBeNotifiedThatFirstNameAndLastNameIsRequired($type, $element)
    {
        $this->assertElementValidationMessage($type, $element, sprintf('Please enter %s.', $element));
    }

    /**
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

    /**
     * @return OrderInterface
     *
     * @throws \RuntimeException
     */
    private function getLastOrder()
    {
        $customer = $this->sharedStorage->get('user')->getCustomer();
        $orders = $this->orderRepository->findByCustomer($customer);
        $lastOrder = end($orders);

        if (false === $lastOrder) {
            throw new \RuntimeException(sprintf('There is no last order for %s', $customer->getFullName()));
        }

        return $lastOrder;
    }
}
