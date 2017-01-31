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
use Sylius\Behat\Context\Ui\Shop\Checkout\AddressingContext;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
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
     * @var SelectShippingPageInterface
     */
    private $selectShippingPage;

    /**
     * @var CompletePageInterface
     */
    private $completePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var AddressingContext
     */
    private $addressingContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param HomePageInterface $homePage
     * @param AddressPageInterface $addressPage
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param SelectShippingPageInterface $selectShippingPage
     * @param CompletePageInterface $completePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param AddressingContext $addressingContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        HomePageInterface $homePage,
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        CurrentPageResolverInterface $currentPageResolver,
        AddressingContext $addressingContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->homePage = $homePage;
        $this->addressPage = $addressPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->selectShippingPage = $selectShippingPage;
        $this->completePage = $completePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->addressingContext = $addressingContext;
    }

    /**
     * @Given I was at the checkout summary step
     */
    public function iWasAtTheCheckoutSummaryStep()
    {
        $this->addressingContext->iSpecifiedTheShippingAddress();
        $this->iProceedOrderWithShippingMethodAndPayment('Free', 'Offline');
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
     * @When /^I proceed selecting ("[^"]+" as shipping country) with "([^"]+)" method$/
     */
    public function iProceedSelectingShippingCountryAndShippingMethod(CountryInterface $shippingCountry = null, $shippingMethodName = null)
    {
        $this->addressingContext->iProceedSelectingShippingCountry($shippingCountry);

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
        $this->iProceedSelectingShippingCountryAndShippingMethod();
        $this->iChoosePaymentMethod($paymentMethodName);
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
     * @When I want to pay for order
     */
    public function iWantToPayForOrder()
    {
        $this->selectPaymentPage->tryToOpen();
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->completePage->confirmOrder();
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
     * @Then I should be on the checkout complete step
     */
    public function iShouldBeOnTheCheckoutCompleteStep()
    {
        Assert::true($this->completePage->isOpen());
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
     * @When I proceed with :shippingMethod shipping method
     */
    public function iProceedOrderWithShippingMethod($shippingMethod)
    {
        $this->iSelectShippingMethod($shippingMethod);
        $this->iCompleteTheShippingStep();
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
            sprintf('The shipping total should be %s, but it is not.', $price)
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
     * @Then /^my order total should be ("(?:\£|\$)\d+(?:\.\d+)?")$/
     */
    public function myOrderTotalShouldBe($total)
    {
        Assert::true(
            $this->completePage->hasOrderTotal($total),
            sprintf('Order total should have %s total, but it does not have.', $total)
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
     * @Then :promotionName should be applied to my order shipping
     */
    public function shouldBeAppliedToMyOrderShipping($promotionName)
    {
        Assert::true($this->completePage->hasShippingPromotion($promotionName));
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
        Assert::same(
            $this->completePage->getPaymentMethodName(),
            $paymentMethod->getName()
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
     * @Then I should be able to go to the complete step again
     */
    public function iShouldBeAbleToGoToTheCompleteStepAgain()
    {
        $this->selectShippingPage->nextStep();

        Assert::true($this->completePage->isOpen());
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
     * @Then there should be information about no available shipping methods
     */
    public function thereShouldBeInformationAboutNoShippingMethodsAvailableForMyShippingAddress()
    {
        Assert::true(
            $this->selectShippingPage->hasNoAvailableShippingMethodsWarning(),
            'There should be warning about no available shipping methods, but it does not.'
        );
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
     * @Then I should be checking out as :email
     */
    public function iShouldBeCheckingOutAs($email)
    {
        Assert::same(
            'Checking out as '.$email.'.',
            $this->selectShippingPage->getPurchaserEmail()
        );
    }

    /**
     * @Then I should not see any information about payment method
     */
    public function iShouldNotSeeAnyInformationAboutPaymentMethod()
    {
        Assert::false(
            $this->completePage->hasPaymentMethod(),
            'There should be no information about payment method, but it is.'
        );
    }

    /**
     * @Then /^(this promotion) should give "([^"]+)" discount$/
     */
    public function thisPromotionShouldGiveDiscount(PromotionInterface $promotion, $discount)
    {
        Assert::same(
            $discount,
            $this->completePage->getShippingPromotionDiscount($promotion->getName())
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
