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
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
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
     * @var AddressPageInterface
     */
    private $addressPage;

    /**
     * @var SelectPaymentPageInterface
     */
    private $selectPaymentPage;

    /**
     * @var ThankYouPageInterface
     */
    private $thankYouPage;

    /**
     * @var SelectShippingPageInterface
     */
    private $selectShippingPage;

    /**
     * @var CompletePageInterface
     */
    private $completePage;

    /**
     * @var ShowPageInterface
     */
    private $orderDetails;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var AddressComparatorInterface
     */
    private $addressComparator;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param HomePageInterface $homePage
     * @param AddressPageInterface $addressPage
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param ThankYouPageInterface $thankYouPage
     * @param SelectShippingPageInterface $selectShippingPage
     * @param CompletePageInterface $completePage
     * @param ShowPageInterface $orderDetails
     * @param OrderRepositoryInterface $orderRepository
     * @param FactoryInterface $addressFactory
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param AddressComparatorInterface $addressComparator
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        HomePageInterface $homePage,
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        ThankYouPageInterface $thankYouPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        ShowPageInterface $orderDetails,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $addressFactory,
        CurrentPageResolverInterface $currentPageResolver,
        AddressComparatorInterface $addressComparator
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->homePage = $homePage;
        $this->addressPage = $addressPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->thankYouPage = $thankYouPage;
        $this->selectShippingPage = $selectShippingPage;
        $this->completePage = $completePage;
        $this->orderDetails = $orderDetails;
        $this->orderRepository = $orderRepository;
        $this->addressFactory = $addressFactory;
        $this->currentPageResolver = $currentPageResolver;
        $this->addressComparator = $addressComparator;
    }

    /**
     * @Given I was at the checkout summary step
     */
    public function iWasAtTheCheckoutSummaryStep()
    {
        $this->iSpecifiedTheShippingAddress($this->createDefaultAddress());
        $this->iProceedOrderWithShippingMethodAndPayment('Free', 'Offline');
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
     * @Given I have proceeded selecting :shippingMethodName shipping method
     */
    public function iHaveProceededSelectingShippingMethod($shippingMethodName)
    {
        $this->iSelectShippingMethod($shippingMethodName);
        $this->selectShippingPage->nextStep();
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
     * @When I try to open checkout addressing page
     */
    public function iTryToOpenCheckoutAddressingPage()
    {
        $this->addressPage->tryToOpen();
    }

    /**
     * @When I try to open checkout shipping page
     */
    public function iTryToOpenCheckoutShippingPage()
    {
        $this->selectShippingPage->tryToOpen();
    }

    /**
     * @When I try to open checkout payment page
     */
    public function iTryToOpenCheckoutPaymentPage()
    {
        $this->selectPaymentPage->tryToOpen();
    }

    /**
     * @When I try to open checkout complete page
     */
    public function iTryToOpenCheckoutCompletePage()
    {
        $this->completePage->tryToOpen();
    }

    /**
     * @When /^I want to browse order details for (this order)$/
     */
    public function iWantToBrowseOrderDetailsForThisOrder(OrderInterface $order)
    {
        $this->orderDetails->open(['tokenValue' => $order->getTokenValue()]);
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
     * @Given I have selected :shippingMethod shipping method
     * @When I select :shippingMethod shipping method
     */
    public function iSelectShippingMethod($shippingMethod)
    {
        $this->selectShippingPage->selectShippingMethod($shippingMethod);
    }

    /**
     * @Then I should not be able to select :shippingMethodName shipping method
     */
    public function iShouldNotBeAbleToSelectShippingMethod($shippingMethodName)
    {
        Assert::false(
            in_array($shippingMethodName, $this->selectShippingPage->getShippingMethods(), true),
            sprintf('Shipping method "%s" should not be available but it does.', $shippingMethodName)
        );
    }

    /**
     * @Then I should have :shippingMethodName shipping method available as the first choice
     */
    public function iShouldHaveShippingMethodAvailableAsFirstChoice($shippingMethodName)
    {
        $shippingMethods = $this->selectShippingPage->getShippingMethods();
        $firstShippingMethod = reset($shippingMethods);

        Assert::same($shippingMethodName, $firstShippingMethod);
    }

    /**
     * @Then I should have :shippingMethodName shipping method available as the last choice
     */
    public function iShouldHaveShippingMethodAvailableAsLastChoice($shippingMethodName)
    {
        $shippingMethods = $this->selectShippingPage->getShippingMethods();
        $lastShippingMethod = end($shippingMethods);

        Assert::same($shippingMethodName, $lastShippingMethod);
    }

    /**
     * @Then I should have :countryName selected as country
     */
    public function iShouldHaveSelectedAsCountry($countryName)
    {
        Assert::eq(
            $countryName,
            $this->addressPage->getShippingAddressCountry(),
            sprintf('Shipping country should be %s but is not.', $countryName)
        );
    }

    /**
     * @Then I should have no country selected
     */
    public function iShouldHaveNoCountrySelected()
    {
        Assert::eq(
            'Select',
            $this->addressPage->getShippingAddressCountry(),
            'Shipping country should not be selected.'
        );
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
     * @When /^I(?:| try to) complete the shipping step$/
     */
    public function iCompleteTheShippingStep()
    {
        $this->selectShippingPage->nextStep();
    }

    /**
     * @When I decide to change my address
     */
    public function iDecideToChangeMyAddress()
    {
        $this->selectShippingPage->changeAddress();
    }

    /**
     * @When I decide to change order shipping method
     */
    public function iDecideToChangeMyShippingMethod()
    {
        $this->selectPaymentPage->changeShippingMethod();
    }

    /**
     * @When I want to browse thank you page
     */
    public function iWantToBrowseThankYouPageForThisOrder()
    {
        $this->thankYouPage->open();
    }

    /**
     * @When I go to the addressing step
     */
    public function iGoToTheAddressingStep()
    {
        if ($this->selectShippingPage->isOpen()) {
            $this->selectShippingPage->changeAddressByStepLabel();

            return;
        }

        if ($this->selectPaymentPage->isOpen()) {
            $this->selectPaymentPage->changeAddressByStepLabel();

            return;
        }

        if ($this->completePage->isOpen()) {
            $this->completePage->changeAddress();

            return;
        }

        throw new UnexpectedPageException('It is impossible to go to addressing step from current page.');
    }

    /**
     * @When I go to the shipping step
     */
    public function iGoToTheShippingStep()
    {
        if ($this->selectPaymentPage->isOpen()) {
            $this->selectPaymentPage->changeShippingMethodByStepLabel();

            return;
        }

        if ($this->completePage->isOpen()) {
            $this->completePage->changeShippingMethod();

            return;
        }

        throw new UnexpectedPageException('It is impossible to go to shipping step from current page.');
    }

    /**
     * @When I decide to change the payment method
     */
    public function iGoToThePaymentStep()
    {
        $this->completePage->changePaymentMethod();
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
     * @When /^I proceed selecting ("[^"]+" as shipping country) with "([^"]+)" method$/
     */
    public function iProceedSelectingShippingCountryAndShippingMethod(CountryInterface $shippingCountry = null, $shippingMethodName)
    {
        $this->iProceedSelectingShippingCountry($shippingCountry);

        $this->selectShippingPage->selectShippingMethod($shippingMethodName ?: 'Free');
        $this->selectShippingPage->nextStep();
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
        $this->selectPaymentPage->selectPaymentMethod($paymentMethodName ?: 'Offline');
        $this->selectPaymentPage->nextStep();
    }

    /**
     * @When I change payment method to :paymentMethodName
     */
    public function iChangePaymentMethodTo($paymentMethodName)
    {
        $this->orderDetails->choosePaymentMethod($paymentMethodName);
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
     * @When I go back to shipping step of the checkout
     */
    public function iGoBackToShippingStepOfTheCheckout()
    {
        $this->selectShippingPage->open();
    }

    /**
     * @Given I have proceeded selecting :paymentMethodName payment method
     * @When /^I (?:proceed|proceeded) selecting "([^"]+)" payment method$/
     */
    public function iProceedSelectingPaymentMethod($paymentMethodName = 'Offline')
    {
        $this->iProceedSelectingShippingCountryAndPaymentMethod(null, $paymentMethodName);
    }

    /**
     * @When /^I change shipping method to "([^"]*)"$/
     */
    public function iChangeShippingMethod($shippingMethodName)
    {
        $this->selectPaymentPage->changeShippingMethod();
        $this->selectShippingPage->selectShippingMethod($shippingMethodName);
        $this->selectShippingPage->nextStep();
    }

    /**
     * @When /^I provide additional note like "([^"]+)"$/
     */
    public function iProvideAdditionalNotesLike($notes)
    {
        $this->sharedStorage->set('additional_note', $notes);
        $this->completePage->addNotes($notes);
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
     * @When I return to the checkout summary step
     */
    public function iReturnToTheCheckoutSummaryStep()
    {
        $this->completePage->open();
    }

    /**
     * @When I want to complete checkout
     */
    public function iWantToCompleteCheckout()
    {
        $this->completePage->tryToOpen();
    }

    /**
     * @Given I have confirmed my order
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->completePage->confirmOrder();
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
     * @Then I should see the thank you page
     */
    public function iShouldSeeTheThankYouPage()
    {
        Assert::true(
            $this->thankYouPage->hasThankYouMessage(),
            'I should see thank you message, but I do not.'
        );
    }

    /**
     * @Then I should not see the thank you page
     */
    public function iShouldNotSeeTheThankYouPage()
    {
        Assert::false(
            $this->thankYouPage->isOpen(),
            'I should not see thank you message, but I do.'
        );
    }

    /**
     * @Given I should be informed with :paymentMethod payment method instructions
     */
    public function iShouldBeInformedWithPaymentMethodInstructions(PaymentMethodInterface $paymentMethod)
    {
        Assert::same(
            $this->thankYouPage->getInstructions(),
            $paymentMethod->getInstructions()
        );
    }

    /**
     * @Then /^I should be redirected (?:|back )to the thank you page$/
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
            $this->selectShippingPage->isOpen(),
            'Checkout shipping page should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be on the checkout payment step
     */
    public function iShouldBeOnTheCheckoutPaymentStep()
    {
        Assert::true(
            $this->selectPaymentPage->isOpen(),
            'Checkout payment page should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be on the checkout summary step
     */
    public function iShouldBeOnTheCheckoutSummaryStep()
    {
        Assert::true(
            $this->completePage->isOpen(),
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
            $this->selectShippingPage->hasNoShippingMethodsMessage(),
            'Shipping page should have no shipping methods message but it does not.'
        );
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
     * @Then my order's shipping address should be to :fullName
     */
    public function iShouldSeeThisShippingAddressAsShippingAddress($fullName)
    {
        $address = $this->sharedStorage->get('shipping_address_'.StringInflector::nameToLowercaseCode($fullName));
        Assert::true(
            $this->completePage->hasShippingAddress($address),
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
            $this->completePage->hasBillingAddress($address),
            'Billing address is improper.'
        );
    }

    /**
     * @Then address to :fullName should be used for both shipping and billing of my order
     */
    public function iShouldSeeThisShippingAddressAsShippingAndBillingAddress($fullName)
    {
        $this->iShouldSeeThisShippingAddressAsShippingAddress($fullName);
        $this->iShouldSeeThisBillingAddressAsBillingAddress($fullName);
    }

    /**
     * @Given I am at the checkout payment step
     * @When I go back to payment step of the checkout
     */
    public function iAmAtTheCheckoutPaymentStep()
    {
        $this->selectPaymentPage->open();
    }

    /**
     * @When I complete the payment step
     */
    public function iCompleteThePaymentStep()
    {
        $this->selectPaymentPage->nextStep();
    }

    /**
     * @When I select :name payment method
     */
    public function iSelectPaymentMethod($name)
    {
        $this->selectPaymentPage->selectPaymentMethod($name);
    }

    /**
     * @When /^I do not modify anything$/
     */
    public function iDoNotModifyAnything()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should not be able to select :paymentMethodName payment method
     */
    public function iShouldNotBeAbleToSelectPaymentMethod($paymentMethodName)
    {
        Assert::false(
            $this->selectPaymentPage->hasPaymentMethod($paymentMethodName),
            sprintf('Payment method "%s" should not be available, but it does.', $paymentMethodName)
        );
    }

    /**
     * @Then I should be able to select :paymentMethodName payment method
     */
    public function iShouldBeAbleToSelectPaymentMethod($paymentMethodName)
    {
        Assert::true(
            $this->selectPaymentPage->hasPaymentMethod($paymentMethodName),
            sprintf('Payment method "%s" should be available, but it does not.', $paymentMethodName)
        );
    }

    /**
     * @Given I have proceeded order with :shippingMethod shipping method and :paymentMethod payment
     * @When I proceed with :shippingMethod shipping method and :paymentMethod payment
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
            $this->completePage->hasItemWithProductAndQuantity($productName, $quantity),
            sprintf('There is no "%s" with quantity %s on order summary page, but it should.', $productName, $quantity)
        );
    }

    /**
     * @Then my order shipping should be :price
     */
    public function myOrderShippingShouldBe($price)
    {
        Assert::true(
            $this->completePage->hasShippingTotal($price),
            sprintf('The shipping total should be %s, but it is not.',$price)
        );
    }

    /**
     * @Then /^the ("[^"]+" product) should have unit price discounted by ("\$\d+")$/
     */
    public function theShouldHaveUnitPriceDiscountedFor(ProductInterface $product, $amount)
    {
        Assert::true(
            $this->completePage->hasProductDiscountedUnitPriceBy($product, $amount),
            sprintf('Product %s should have discounted price by %s, but it does not have.', $product->getName(), $amount)
        );
    }

    /**
     * @Then /^my order total should be ("(?:\£|\$)\d+")$/
     */
    public function myOrderTotalShouldBe($total)
    {
        Assert::true(
            $this->completePage->hasOrderTotal($total),
            sprintf('Order total should have %s total, but it does not have.', $total)
        );
    }

    /**
     * @Then /^my order total in base currency should be ("(?:\£|\$)\d+")$/
     */
    public function myOrderTotalInBaseCurrencyShouldBe($total)
    {
        Assert::same(
            $total,
            $this->completePage->getBaseCurrencyOrderTotal(),
            'Order total should have %s total, but it has %s.'
        );
    }

    /**
     * @Then my order promotion total should be :promotionTotal
     */
    public function myOrderPromotionTotalShouldBe($promotionTotal)
    {
        Assert::true(
            $this->completePage->hasPromotionTotal($promotionTotal),
            sprintf('The total discount should be %s, but it does not.', $promotionTotal)
        );
    }

    /**
     * @Then :promotionName should be applied to my order
     */
    public function shouldBeAppliedToMyOrder($promotionName)
    {
        Assert::true(
            $this->completePage->hasPromotion($promotionName),
            sprintf('The promotion %s should appear on the page, but it does not.', $promotionName)
        );
    }

    /**
     * @Given my tax total should be :taxTotal
     */
    public function myTaxTotalShouldBe($taxTotal)
    {
        Assert::true(
            $this->completePage->hasTaxTotal($taxTotal),
            sprintf('The tax total should be %s, but it does not.', $taxTotal)
        );
    }

    /**
     * @Then my order's shipping method should be :shippingMethod
     */
    public function myOrderSShippingMethodShouldBe(ShippingMethodInterface $shippingMethod)
    {
        Assert::true(
            $this->completePage->hasShippingMethod($shippingMethod),
            sprintf('I should see %s shipping method, but I do not.', $shippingMethod->getName())
        );
    }

    /**
     * @Then my order's payment method should be :paymentMethod
     */
    public function myOrderSPaymentMethodShouldBe(PaymentMethodInterface $paymentMethod)
    {
        Assert::true(
            $this->completePage->hasPaymentMethod($paymentMethod),
            sprintf('I should see %s payment method, but I do not.', $paymentMethod->getName())
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
            $this->addressPage->isOpen(),
            'Checkout addressing step should be opened, but it is not.'
        );
    }

    /**
     * @Given I should be able to go to the shipping step again
     */
    public function iShouldBeAbleToGoToTheShippingStepAgain()
    {
        $this->addressPage->nextStep();

        Assert::true(
            $this->selectShippingPage->isOpen(),
            'Checkout shipping step should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be redirected to the shipping step
     */
    public function iShouldBeRedirectedToTheShippingStep()
    {
        Assert::true(
            $this->selectShippingPage->isOpen(),
            'Checkout shipping step should be opened, but it is not.'
        );
    }

    /**
     * @Then /^I should be able to pay(?| again)$/
     */
    public function iShouldBeAbleToPayAgain()
    {
        Assert::true(
            $this->orderDetails->hasPayAction(),
            'I should be able to pay, but I am not able to.'
        );
    }

    /**
     * @Then I should not be able to pay
     */
    public function iShouldNotBeAbleToPayAgain()
    {
        Assert::false(
            $this->orderDetails->hasPayAction(),
            'I should not be able to pay, but I am able to.'
        );
    }

    /**
     * @Given I should be able to go to the payment step again
     */
    public function iShouldBeAbleToGoToThePaymentStepAgain()
    {
        $this->selectShippingPage->nextStep();

        Assert::true(
            $this->selectPaymentPage->isOpen(),
            'Checkout payment step should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be redirected to the payment step
     */
    public function iShouldBeRedirectedToThePaymentStep()
    {
        Assert::true(
            $this->selectPaymentPage->isOpen(),
            'Checkout payment step should be opened, but it is not.'
        );
    }

    /**
     * @Given I should be able to go to the summary page again
     */
    public function iShouldBeAbleToGoToTheSummaryPageAgain()
    {
        $this->selectPaymentPage->nextStep();

        Assert::true(
            $this->completePage->isOpen(),
            'Checkout summary page should be opened, but it is not.'
        );
    }

    /**
     * @Given I should see shipping method :shippingMethodName with fee :fee
     */
    public function iShouldSeeShippingFee($shippingMethodName, $fee)
    {
        Assert::true(
            $this->selectShippingPage->hasShippingMethodFee($shippingMethodName, $fee),
            sprintf('The shipping fee should be %s, but it does not.', $fee)
        );
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
     * @Then the subtotal of :item item should be :price
     */
    public function theSubtotalOfItemShouldBe($item, $price)
    {
        $currentPage = $this->resolveCurrentStepPage();
        $actualPrice = $currentPage->getItemSubtotal($item);

        Assert::eq(
            $actualPrice,
            $price,
            sprintf('The %s subtotal should be %s, but is %s', $item, $price, $actualPrice)
        );
    }

    /**
     * @Then the :product product should have unit price :price
     */
    public function theProductShouldHaveUnitPrice(ProductInterface $product, $price)
    {
        Assert::true(
            $this->completePage->hasProductUnitPrice($product, $price),
            sprintf('Product %s should have unit price %s, but it does not have.', $product->getName(), $price)
        );
    }

    /**
     * @Given /^I should be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
        Assert::true(
            $this->completePage->hasProductOutOfStockValidationMessage($product),
            sprintf('I should see validation message for %s product', $product->getName())
        );
    }

    /**
     * @Then my order's locale should be :localeName
     */
    public function myOrderSLocaleShouldBe($localeName)
    {
        Assert::true(
            $this->completePage->hasLocale($localeName),
            'Order locale code is improper.'
        );
    }

    /**
     * @Then my order's currency should be :currencyCode
     */
    public function myOrderSCurrencyShouldBe($currencyCode)
    {
        Assert::true(
            $this->completePage->hasCurrency($currencyCode),
            'Order currency code is improper.'
        );
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
        Assert::false(
            $this->completePage->hasProductOutOfStockValidationMessage($product),
            sprintf('I should see validation message for %s product', $product->getName())
        );
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
     * @Then I should not be able to specify province name manually for shipping address
     */
    public function iShouldNotBeAbleToSpecifyProvinceNameManuallyForShippingAddress()
    {
        Assert::false(
            $this->addressPage->hasShippingAddressInput(),
            'I should not be able to specify manually the province for shipping address, but I can.'
        );
    }

    /**
     * @Then I should not be able to specify province name manually for billing address
     */
    public function iShouldNotBeAbleToSpecifyProvinceNameManuallyForBillingAddress()
    {
        Assert::false(
            $this->addressPage->hasBillingAddressInput(),
            'I should not be able to specify manually the province for billing address, but I can.'
        );
    }

    /**
     * @Then I should see :provinceName in the shipping address
     */
    public function iShouldSeeInTheShippingAddress($provinceName)
    {
        Assert::true(
            $this->completePage->hasShippingProvinceName($provinceName),
            sprintf('Cannot find shipping address with province %s', $provinceName)
        );
    }

    /**
     * @Then I should see :provinceName in the billing address
     */
    public function iShouldSeeInTheBillingAddress($provinceName)
    {
        Assert::true(
            $this->completePage->hasBillingProvinceName($provinceName),
            sprintf('Cannot find billing address with province %s', $provinceName)
        );
    }

    /**
     * @When I do not select any shipping method
     */
    public function iDoNotSelectAnyShippingMethod()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should still be on the shipping step
     */
    public function iShouldStillBeOnTheShippingStep()
    {
        Assert::true(
            $this->selectShippingPage->isOpen(),
            'Select shipping page should be open, but it does not.'
        );
    }

    /**
     * @Then I should be notified that the shipping method is required
     */
    public function iShouldBeNotifiedThatTheShippingMethodIsRequired()
    {
        Assert::same(
            $this->selectShippingPage->getValidationMessageForShipment(),
            'Please select shipping method.'
        );
    }

    /**
     * @Then there should be information about no available shipping methods
     * @Then there should be information about no shipping methods available for my shipping address
     */
    public function thereShouldBeInformationAboutNoShippingMethodsAvailableForMyShippingAddress()
    {
        Assert::true(
            $this->selectShippingPage->hasNoAvailableShippingMethodsWarning(),
            'There should be warning about no available shipping methods, but it does not.'
        );
    }

    /**
     * @Then I should not be able to complete the shipping step
     */
    public function iShouldNotBeAbleToCompleteTheShippingStep()
    {
        Assert::true(
            $this->selectShippingPage->isNextStepButtonUnavailable(),
            'The next step button should be disabled, but it does not.'
        );
    }

    /**
     * @When I do not select any payment method
     */
    public function iDoNotSelectAnyPaymentMethod()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should not be able to complete the payment step
     */
    public function iShouldNotBeAbleToCompleteThePaymentStep()
    {
        Assert::true(
            $this->selectPaymentPage->isNextStepButtonUnavailable(),
           'The "next step" button should be disabled.'
        );
    }

    /**
     * @Then there should be information about no payment methods available for my order
     */
    public function thereShouldBeInformationAboutNoPaymentMethodsAvailableForMyOrder()
    {
        Assert::true(
            $this->selectPaymentPage->hasNoAvailablePaymentMethodsWarning(),
            'There should be warning about no available payment methods, but it does not.'
        );
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
     * @Then I should have :paymentMethodName payment method available as the first choice
     */
    public function iShouldHavePaymentMethodAvailableAsFirstChoice($paymentMethodName)
    {
        $paymentMethods = $this->selectPaymentPage->getPaymentMethods();
        $firstPaymentMethod = reset($paymentMethods);

        Assert::same($paymentMethodName, $firstPaymentMethod);
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the last choice
     */
    public function iShouldHavePaymentMethodAvailableAsLastChoice($paymentMethodName)
    {
        $paymentMethods = $this->selectPaymentPage->getPaymentMethods();
        $lastPaymentMethod = end($paymentMethods);

        Assert::same($paymentMethodName, $lastPaymentMethod);
    }

    /**
     * @Then I should see :shippingMethodName shipping method
     */
    public function iShouldSeeShippingMethod($shippingMethodName)
    {
        Assert::true(
            $this->selectShippingPage->hasShippingMethod($shippingMethodName),
            sprintf('There should be %s shipping method, but it is not.', $shippingMethodName)
        );
    }

    /**
     * @Then I should not see :shippingMethodName shipping method
     */
    public function iShouldNotSeeShippingMethod($shippingMethodName)
    {
        Assert::false(
            $this->selectShippingPage->hasShippingMethod($shippingMethodName),
            sprintf('There should not be %s shipping method, but it is.', $shippingMethodName)
        );
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

    /**
     * @return SymfonyPageInterface
     */
    private function resolveCurrentStepPage()
    {
        $possiblePages = [
            $this->addressPage,
            $this->selectPaymentPage,
            $this->selectShippingPage,
            $this->completePage,
        ];

        return $this->currentPageResolver->getCurrentPageWithForm($possiblePages);
    }
}
