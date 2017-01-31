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
use Sylius\Behat\Context\Ui\Shop\Checkout\PaymentContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\ShippingContext;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
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
     * @var ShippingContext
     */
    private $shippingContext;

    /**
     * @var PaymentContext
     */
    private $paymentContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param AddressPageInterface $addressPage
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param SelectShippingPageInterface $selectShippingPage
     * @param CompletePageInterface $completePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param AddressingContext $addressingContext
     * @param ShippingContext $shippingContext
     * @param PaymentContext $paymentContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        CurrentPageResolverInterface $currentPageResolver,
        AddressingContext $addressingContext,
        ShippingContext $shippingContext,
        PaymentContext $paymentContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->addressPage = $addressPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->selectShippingPage = $selectShippingPage;
        $this->completePage = $completePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->addressingContext = $addressingContext;
        $this->shippingContext = $shippingContext;
        $this->paymentContext = $paymentContext;
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
     * @When I try to open checkout complete page
     */
    public function iTryToOpenCheckoutCompletePage()
    {
        $this->completePage->tryToOpen();
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

        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethodName ?: 'Free');
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
     * @Given I have proceeded selecting :paymentMethodName payment method
     * @When /^I (?:proceed|proceeded) selecting "([^"]+)" payment method$/
     */
    public function iProceedSelectingPaymentMethod($paymentMethodName = 'Offline')
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod();
        $this->paymentContext->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @When /^I change shipping method to "([^"]*)"$/
     */
    public function iChangeShippingMethod($shippingMethodName)
    {
        $this->paymentContext->iDecideToChangeMyShippingMethod();
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethodName);
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
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->completePage->confirmOrder();
    }

    /**
     * @Then I should be on the checkout complete step
     */
    public function iShouldBeOnTheCheckoutCompleteStep()
    {
        Assert::true($this->completePage->isOpen());
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
     * @When /^I do not modify anything$/
     */
    public function iDoNotModifyAnything()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Given I have proceeded order with :shippingMethod shipping method and :paymentMethod payment
     * @When I proceed with :shippingMethod shipping method and :paymentMethod payment
     */
    public function iProceedOrderWithShippingMethodAndPayment($shippingMethod, $paymentMethod)
    {
        $this->iProceedOrderWithShippingMethod($shippingMethod);
        $this->paymentContext->iChoosePaymentMethod($paymentMethod);
    }

    /**
     * @When I proceed with :shippingMethod shipping method
     */
    public function iProceedOrderWithShippingMethod($shippingMethod)
    {
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethod);
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
     * @return AddressPageInterface|SelectPaymentPageInterface|SelectShippingPageInterface|CompletePageInterface
     */
    private function resolveCurrentStepPage()
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->addressPage,
            $this->selectPaymentPage,
            $this->selectShippingPage,
            $this->completePage,
        ]);
    }
}
