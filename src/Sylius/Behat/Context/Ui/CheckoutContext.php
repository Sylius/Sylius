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
use Sylius\Behat\Page\Checkout\CheckoutAddressingStep;
use Sylius\Behat\Page\Checkout\CheckoutFinalizeStep;
use Sylius\Behat\Page\Checkout\CheckoutPaymentStep;
use Sylius\Behat\Page\Checkout\CheckoutShippingStep;
use Sylius\Behat\Page\Checkout\CheckoutThankYouPage;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

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
     * @var CheckoutAddressingStep
     */
    private $checkoutAddressingStep;

    /**
     * @var CheckoutShippingStep
     */
    private $checkoutShippingStep;

    /**
     * @var CheckoutPaymentStep
     */
    private $checkoutPaymentStep;

    /**
     * @var CheckoutFinalizeStep
     */
    private $checkoutFinalizeStep;

    /**
     * @var CheckoutThankYouPage
     */
    private $checkoutThankYouPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CheckoutAddressingStep $checkoutAddressingStep
     * @param CheckoutShippingStep $checkoutShippingStep
     * @param CheckoutPaymentStep $checkoutPaymentStep
     * @param CheckoutFinalizeStep $checkoutFinalizeStep
     * @param CheckoutThankYouPage $checkoutThankYouPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CheckoutAddressingStep $checkoutAddressingStep,
        CheckoutShippingStep $checkoutShippingStep,
        CheckoutPaymentStep $checkoutPaymentStep,
        CheckoutFinalizeStep $checkoutFinalizeStep,
        CheckoutThankYouPage $checkoutThankYouPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->checkoutAddressingStep = $checkoutAddressingStep;
        $this->checkoutShippingStep = $checkoutShippingStep;
        $this->checkoutPaymentStep = $checkoutPaymentStep;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
        $this->checkoutThankYouPage = $checkoutThankYouPage;
    }

    /**
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingOfflinePaymentMethod($paymentMethodName)
    {
        $this->proceedOrder(null, null, $paymentMethodName);
    }

    /**
     * @When /^I proceed selecting "([^"]+)" shipping method$/
     * @Given /^I chose "([^"]*)" shipping method$/
     */
    public function iProceedSelectingShippingMethod($shippingMethodName)
    {
        $this->proceedOrder(null, $shippingMethodName);
    }

    /**
     * @When /^I proceed selecting "([^"]*)" as shipping country with "([^"]*)" method$/
     */
    public function iProceedSelectingShippingCountryAndShippingMethod($shippingCountry, $shippingMethodName)
    {
        $this->proceedOrder($shippingCountry, $shippingMethodName);
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
        $user = $this->sharedStorage->getCurrentResource('user');
        $customer = $user->getCustomer();

        expect($this->checkoutThankYouPage->hasThankYouMessageFor($customer->getFullName()))->toBe(true);
    }

    /**
     * @param string|null $shippingCountry
     * @param string|null $shippingMethodName
     * @param string|null $paymentMethodName
     */
    private function proceedOrder($shippingCountry = null, $shippingMethodName = null, $paymentMethodName = null)
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

        $this->checkoutShippingStep->selectShippingMethod($shippingMethodName ?: 'Free');
        $this->checkoutShippingStep->continueCheckout();

        $this->checkoutPaymentStep->selectPaymentMethod($paymentMethodName ?: 'Offline');
        $this->checkoutPaymentStep->continueCheckout();
    }
}
