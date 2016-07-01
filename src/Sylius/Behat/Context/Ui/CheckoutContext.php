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
use Sylius\Behat\Page\Shop\Checkout\SummaryPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @var AddressingPageInterface
     */
    private $addressingPage;

    /**
     * @var PaymentPageInterface
     */
    private $paymentPage;

    /**
     * @var ThankYouPageInterface
     */
    private $thankYouPage;

    /**
     * @var ShippingPageInterface
     */
    private $shippingPage;

    /**
     * @var SummaryPageInterface
     */
    private $summaryPage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param HomePageInterface $homePage
     * @param AddressingPageInterface $addressingPage
     * @param PaymentPageInterface $paymentPage
     * @param ThankYouPageInterface $thankYouPage
     * @param ShippingPageInterface $shippingPage
     * @param SummaryPageInterface $summaryPage
     * @param OrderRepositoryInterface $orderRepository
     * @param SecurityServiceInterface $securityService
     * @param FactoryInterface $addressFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        HomePageInterface $homePage,
        AddressingPageInterface $addressingPage,
        PaymentPageInterface $paymentPage,
        ThankYouPageInterface $thankYouPage,
        ShippingPageInterface $shippingPage,
        SummaryPageInterface $summaryPage,
        OrderRepositoryInterface $orderRepository,
        SecurityServiceInterface $securityService,
        FactoryInterface $addressFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->homePage = $homePage;
        $this->addressingPage = $addressingPage;
        $this->paymentPage = $paymentPage;
        $this->thankYouPage = $thankYouPage;
        $this->shippingPage = $shippingPage;
        $this->summaryPage = $summaryPage;
        $this->orderRepository = $orderRepository;
        $this->securityService = $securityService;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @Given /^I proceed without selecting shipping address$/
     */
    public function iProceedWithoutSelectingShippingAddress()
    {
        $this->addressingPage->open();
        $this->addressingPage->nextStep();
    }

    /**
     * @Given I am at the checkout addressing step
     */
    public function iAmAtTheCheckoutAddressingStep()
    {
        $this->addressingPage->open();
    }

    /**
     * @Given /^(this user) bought this product$/
     */
    public function thisUserBought(UserInterface $user)
    {
        $this->securityService->performActionAs($user, function () {
            $this->iProceedSelectingOfflinePaymentMethod();
            $this->iConfirmMyOrder();
        });
    }

    /**
     * @When /^I specify the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I (do not specify any shipping address) information$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address)
    {
        $key = sprintf(
            'shipping_address_%s_%s',
            strtolower($address->getFirstName()),
            strtolower($address->getLastName())
        );
        $this->sharedStorage->set($key, $address);

        $this->addressingPage->specifyShippingAddress($address);
    }

    /**
     * @When I specify shipping country province as :province
     */
    public function iSpecifyShippingCountryProvinceAs($province)
    {
        $this->addressingPage->specifyShippingAddressProvince($province);
    }

    /**
     * @When I specify billing country province as :province
     */
    public function iSpecifyBillingCountryProvinceAs($province)
    {
        $this->addressingPage->specifyBillingAddressProvince($province);
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

        $this->addressingPage->specifyBillingAddress($address);
    }

    /**
     * @When /^I specified the shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifiedTheShippingAddress(AddressInterface $address)
    {
        $this->addressingPage->open();
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
     * @When I go back to store
     */
    public function iGoBackToStore()
    {
        $this->addressingPage->backToStore();
    }

    /**
     * @When I complete the shipping step
     */
    public function iCompleteTheShippingStep()
    {
        $this->shippingPage->nextStep();
    }

    /**
     * @When I decide to change my address
     */
    public function iDecideToChangeMyAddress()
    {
        $this->shippingPage->changeAddress();
    }

    /**
     * @When I decide to change order shipping method
     */
    public function iDecideToChangeMyShippingMethod()
    {
        $this->paymentPage->changeShippingMethod();
    }

    /**
     * @When I go to the addressing step
     */
    public function iGoToTheAddressingStep()
    {
        if ($this->shippingPage->isOpen()) {
            $this->shippingPage->changeAddressByStepLabel();

            return;
        }

        if ($this->paymentPage->isOpen()) {
            $this->paymentPage->changeAddressByStepLabel();

            return;
        }

        if ($this->summaryPage->isOpen()) {
            $this->summaryPage->changeAddress();

            return;
        }

        throw new UnexpectedPageException('It is impossible to go to addressing step from current page.');
    }

    /**
     * @When I go to the shipping step
     */
    public function iGoToTheShippingStep()
    {
        if ($this->paymentPage->isOpen()) {
            $this->paymentPage->changeShippingMethodByStepLabel();

            return;
        }

        if ($this->summaryPage->isOpen()) {
            $this->summaryPage->changeShippingMethod();

            return;
        }

        throw new UnexpectedPageException('It is impossible to go to shipping step from current page.');
    }

    /**
     * @When I go to the payment step
     */
    public function iGoToThePaymentStep()
    {
        $this->summaryPage->changePaymentMethod();
    }

    /**
     * @When /^I proceed selecting ("[^"]+" as shipping country)$/
     */
    public function iProceedSelectingShippingCountry(CountryInterface $shippingCountry = null)
    {
        $this->addressingPage->open();
        $shippingAddress = $this->createDefaultAddress();
        !$shippingCountry ?: $shippingAddress->setCountryCode($shippingCountry->getCode());

        $this->addressingPage->specifyShippingAddress($shippingAddress);
        $this->addressingPage->nextStep();
    }

    /**
     * @When /^I proceed selecting ("[^"]+" as shipping country) with "([^"]+)" method$/
     */
    public function iProceedSelectingShippingCountryAndShippingMethod(CountryInterface $shippingCountry = null, $shippingMethodName)
    {
        $this->iProceedSelectingShippingCountry($shippingCountry);

        $this->shippingPage->selectShippingMethod($shippingMethodName ?: 'Free');
        $this->shippingPage->nextStep();
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
        $this->paymentPage->selectPaymentMethod($paymentMethodName ?: 'Offline');
        $this->paymentPage->nextStep();
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
     * @Given I have proceeded selecting :paymentMethodName payment method
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingOfflinePaymentMethod($paymentMethodName = 'Offline')
    {
        $this->iProceedSelectingShippingCountryAndPaymentMethod(null, $paymentMethodName);
    }

    /**
     * @When /^I change shipping method to "([^"]*)"$/
     */
    public function iChangeShippingMethod($shippingMethodName)
    {
        $this->paymentPage->changeShippingMethod();
        $this->shippingPage->selectShippingMethod($shippingMethodName);
        $this->shippingPage->nextStep();
    }

    /**
     * @When /^I provide additional note like "([^"]+)"$/
     */
    public function iProvideAdditionalNotesLike($notes)
    {
        $this->sharedStorage->set('additional_note', $notes);
        $this->summaryPage->addNotes($notes);
    }

    /**
     * @When /^I proceed as guest "([^"]*)" with ("[^"]+" as shipping country)$/
     */
    public function iProceedLoggingAsGuestWithAsShippingCountry($email, CountryInterface $shippingCountry = null)
    {
        $this->addressingPage->open();
        $this->addressingPage->specifyEmail($email);
        $shippingAddress = $this->createDefaultAddress();
        !$shippingCountry ?: $shippingAddress->setCountryCode($shippingCountry->getCode());

        $this->addressingPage->specifyShippingAddress($shippingAddress);
        $this->addressingPage->nextStep();
    }

    /**
     * @Given I have confirmed my order
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->summaryPage->confirmOrder();
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
        Assert::true(
            $this->thankYouPage->hasThankYouMessage(),
            'I should see thank you message, but I do not'
        );
    }

    /**
     * @Then I should be redirected back to the thank you page
     */
    public function iShouldBeRedirectedBackToTheThankYouPage()
    {
        $this->thankYouPage->waitForResponse(5);

        Assert::true(
            $this->thankYouPage->isOpen(),
            'I should be on thank you page, but I am not.'
        );
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
     * @Then I should be on the checkout summary step
     */
    public function iShouldBeOnTheCheckoutSummaryStep()
    {
        Assert::true(
            $this->summaryPage->isOpen(),
            'Checkout summary page should be opened, but it is not.'
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
     * @Then my order's shipping address should be to :fullName
     */
    public function iShouldSeeThisShippingAddressAsShippingAddress($fullName)
    {
        $address = $this->sharedStorage->get('shipping_address_'.StringInflector::nameToLowercaseCode($fullName));
        Assert::true(
            $this->summaryPage->hasShippingAddress($address),
            'Shipping address is improper.'
        );
    }

    /**
     * @Then my order's billing address should be to :fullName
     */
    public function iShouldSeeThisBillingAddressAsBillingAddress($fullName)
    {
        $address = $this->sharedStorage->get('billing_address_'.StringInflector::nameToLowercaseCode($fullName));
        Assert::true(
            $this->summaryPage->hasBillingAddress($address),
            'Billing address is improper.'
        );
    }

    /**
     * @Then address to :fullName should be used for both shipping and billing of my order`
     */
    public function iShouldSeeThisShippingAddressAsShippingAndBillingAddress($fullName)
    {
        $this->iShouldSeeThisShippingAddressAsShippingAddress($fullName);
        $this->iShouldSeeThisBillingAddressAsBillingAddress($fullName);
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
     * @When I proceed order with :shippingMethod shipping method and :paymentMethod payment
     */
    public function iProceedOrderWithShippingMethodAndPayment($shippingMethod, $paymentMethod)
    {
        $this->iSelectShippingMethod($shippingMethod);
        $this->iCompleteTheShippingStep();
        $this->iSelectPaymentMethod($paymentMethod);
        $this->iCompleteThePaymentStep();
    }

    /**
     * @Given I should have :quantity :productName products in the cart
     */
    public function iShouldHaveProductsInTheCart($quantity, $productName)
    {
        Assert::true(
            $this->summaryPage->hasItemWithProductAndQuantity($productName, $quantity),
            sprintf('There is no "%s" with quantity %s on order summary page, but it should.', $productName, $quantity)
        );
    }

    /**
     * @Then my order shipping should be :price
     */
    public function myOrderShippingShouldBe($price)
    {
        Assert::true(
            $this->summaryPage->hasShippingTotal($price),
            sprintf('The shipping total should be %s, but it is not.',$price)
        );
    }

    /**
     * @Then /^the ("[^"]+" product) should have unit price discounted by ("\$\d+")$/
     */
    public function theShouldHaveUnitPriceDiscountedFor(ProductInterface $product, $amount)
    {
        Assert::true(
            $this->summaryPage->hasProductDiscountedUnitPriceBy($product, $amount),
            sprintf('Product %s should have discounted price by %s, but it does not have.', $product->getName(), $amount)
        );
    }

    /**
     * @Then /^my order total should be ("\$\d+")$/
     */
    public function myOrderTotalShouldBe($total)
    {
        Assert::true(
            $this->summaryPage->hasOrderTotal($total),
            sprintf('Order total should have %s total, but it does not have.', $total)
        );
    }

    /**
     * @Then my order promotion total should be :promotionTotal
     */
    public function myOrderPromotionTotalShouldBe($promotionTotal)
    {
        Assert::true(
            $this->summaryPage->hasPromotionTotal($promotionTotal),
            sprintf('The total discount should be %s, but it does not.', $promotionTotal)
        );
    }

    /**
     * @Then :promotionName should be applied to my order
     */
    public function shouldBeAppliedToMyOrder($promotionName)
    {
        Assert::true(
            $this->summaryPage->hasPromotion($promotionName),
            sprintf('The promotion %s should appear on the page, but it does not.', $promotionName)
        );
    }

    /**
     * @Given my tax total should be :taxTotal
     */
    public function myTaxTotalShouldBe($taxTotal)
    {
        Assert::true(
            $this->summaryPage->hasTaxTotal($taxTotal),
            sprintf('The tax total should be %s, but it does not.', $taxTotal)
        );
    }

    /**
     * @Then my order's shipping method should be :shippingMethod
     */
    public function myOrderSShippingMethodShouldBe(ShippingMethodInterface $shippingMethod)
    {
        Assert::true(
            $this->summaryPage->hasShippingMethod($shippingMethod),
            sprintf('I should see %s shipping method, but I do not.', $shippingMethod->getName())
        );
    }

    /**
     * @Then my order's payment method should be :paymentMethod
     */
    public function myOrderSPaymentMethodShouldBe(PaymentMethodInterface $paymentMethod)
    {
        Assert::true(
            $this->summaryPage->hasPaymentMethod($paymentMethod),
            sprintf('I should see %s payment method, but i do not.', $paymentMethod->getName())
        );
    }

    /**
     * @Then I should be redirected to the homepage
     */
    public function iShouldBeRedirectedToTheHomepage()
    {
        Assert::true(
            $this->homePage->isOpen(),
            'Shop homepage should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be redirected to the addressing step
     */
    public function iShouldBeRedirectedToTheAddressingStep()
    {
        Assert::true(
            $this->addressingPage->isOpen(),
            'Checkout addressing step should be opened, but it is not.'
        );
    }

    /**
     * @Given I should be able to go to the shipping step again
     */
    public function iShouldBeAbleToGoToTheShippingStepAgain()
    {
        $this->addressingPage->nextStep();

        Assert::true(
            $this->shippingPage->isOpen(),
            'Checkout shipping step should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be redirected to the shipping step
     */
    public function iShouldBeRedirectedToTheShippingStep()
    {
        Assert::true(
            $this->shippingPage->isOpen(),
            'Checkout shipping step should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be able to pay again
     */
    public function iShouldBeAbleToPayAgain()
    {
        Assert::true(
            $this->thankYouPage->isOpen(),
            'I should be on thank you page, but I am not.'
        );

        Assert::true(
            $this->thankYouPage->hasPayAction(),
            'I should be able to pay, but I am not able to.'
        );
    }

    /**
     * @Given I should be able to go to the payment step again
     */
    public function iShouldBeAbleToGoToThePaymentStepAgain()
    {
        $this->shippingPage->nextStep();

        Assert::true(
            $this->paymentPage->isOpen(),
            'Checkout payment step should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be redirected to the payment step
     */
    public function iShouldBeRedirectedToThePaymentStep()
    {
        Assert::true(
            $this->paymentPage->isOpen(),
            'Checkout payment step should be opened, but it is not.'
        );
    }

    /**
     * @Given I should be able to go to the summary page again
     */
    public function iShouldBeAbleToGoToTheSummaryPageAgain()
    {
        $this->paymentPage->nextStep();

        Assert::true(
            $this->summaryPage->isOpen(),
            'Checkout summary page should be opened, but it is not.'
        );
    }

    /**
     * @Given I should see shipping method :shippingMethodName with fee :fee
     */
    public function iShouldSeeShippingFee($shippingMethodName, $fee)
    {
        Assert::true(
            $this->shippingPage->hasShippingMethodFee($shippingMethodName, $fee), 
            sprintf('The shipping fee should be %s, but it does not.', $fee)
        );
    }

    /**
     * @When /^I complete addressing step with email "([^"]+)" and ("([^"]+)" as shipping country)$/
     */
    public function iCompleteAddressingStepWithEmail($email, AddressInterface $address)
    {
        $this->iSpecifiedTheShippingAddress($address);
        $this->iSpecifyTheEmail($email);
        $this->iCompleteTheAddressingStep();
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
        $address->setCountryCode('FR');
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
            $this->addressingPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('The %s should be required.', $element)
        );
    }
}
