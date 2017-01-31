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
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CheckoutContext implements Context
{
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
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        CurrentPageResolverInterface $currentPageResolver,
        AddressingContext $addressingContext,
        ShippingContext $shippingContext,
        PaymentContext $paymentContext
    ) {
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
     * @Then the subtotal of :item item should be :price
     */
    public function theSubtotalOfItemShouldBe($item, $price)
    {
        $currentPage = $this->resolveCurrentStepPage();
        $actualPrice = $currentPage->getItemSubtotal($item);

        Assert::eq($actualPrice, $price, sprintf('The %s subtotal should be %s, but is %s', $item, $price, $actualPrice));
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
