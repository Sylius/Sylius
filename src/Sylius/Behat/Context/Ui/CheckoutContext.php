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
use Sylius\Behat\Page\Shop\Order\OrderPaymentsPageInterface;
use Sylius\Behat\Page\Shop\Checkout\AddressingStepInterface;
use Sylius\Behat\Page\Shop\Checkout\FinalizeStepInterface;
use Sylius\Behat\Page\Shop\Checkout\PaymentStepInterface;
use Sylius\Behat\Page\Shop\Checkout\SecurityStepInterface;
use Sylius\Behat\Page\Shop\Checkout\ShippingStepInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

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
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityStepInterface $checkoutSecurityStep
     * @param AddressingStepInterface $checkoutAddressingStep
     * @param ShippingStepInterface $checkoutShippingStep
     * @param PaymentStepInterface $checkoutPaymentStep
     * @param FinalizeStepInterface $checkoutFinalizeStep
     * @param ThankYouPageInterface $checkoutThankYouPage
     * @param OrderPaymentsPageInterface $orderPaymentsPage
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityStepInterface $checkoutSecurityStep,
        AddressingStepInterface $checkoutAddressingStep,
        ShippingStepInterface $checkoutShippingStep,
        PaymentStepInterface $checkoutPaymentStep,
        FinalizeStepInterface $checkoutFinalizeStep,
        ThankYouPageInterface $checkoutThankYouPage,
        OrderPaymentsPageInterface $orderPaymentsPage,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->checkoutSecurityStep = $checkoutSecurityStep;
        $this->checkoutAddressingStep = $checkoutAddressingStep;
        $this->checkoutShippingStep = $checkoutShippingStep;
        $this->checkoutPaymentStep = $checkoutPaymentStep;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
        $this->checkoutThankYouPage = $checkoutThankYouPage;
        $this->orderPaymentsPage = $orderPaymentsPage;
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
