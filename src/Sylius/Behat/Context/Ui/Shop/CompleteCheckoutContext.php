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
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompleteCheckoutContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ThankYouPageInterface
     */
    private $thankYouPage;

    /**
     * @var CompletePageInterface
     */
    private $completePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ThankYouPageInterface $thankYouPage
     * @param CompletePageInterface $completePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ThankYouPageInterface $thankYouPage,
        CompletePageInterface $completePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->thankYouPage = $thankYouPage;
        $this->completePage = $completePage;
    }

    /**
     * @When I decide to change the payment method
     */
    public function iGoToThePaymentStep()
    {
        $this->completePage->changePaymentMethod();
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
     * @Given I have confirmed my order
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->completePage->confirmOrder();
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
}
