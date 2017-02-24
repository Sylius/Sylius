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
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutAddressingContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutPaymentContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutShippingContext;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
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
     * @var CheckoutAddressingContext
     */
    private $addressingContext;

    /**
     * @var CheckoutShippingContext
     */
    private $shippingContext;

    /**
     * @var CheckoutPaymentContext
     */
    private $paymentContext;

    /**
     * @param AddressPageInterface $addressPage
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param SelectShippingPageInterface $selectShippingPage
     * @param CompletePageInterface $completePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param CheckoutAddressingContext $addressingContext
     * @param CheckoutShippingContext $shippingContext
     * @param CheckoutPaymentContext $paymentContext
     */
    public function __construct(
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        CurrentPageResolverInterface $currentPageResolver,
        CheckoutAddressingContext $addressingContext,
        CheckoutShippingContext $shippingContext,
        CheckoutPaymentContext $paymentContext
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
     * @Given I chose :shippingMethodName shipping method
     * @When I proceed selecting :shippingMethodName shipping method
     */
    public function iProceedSelectingShippingMethod($shippingMethodName)
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod(null, $shippingMethodName);
    }

    /**
     * @Given I have proceeded selecting :paymentMethodName payment method
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingPaymentMethod($paymentMethodName)
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod();
        $this->paymentContext->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @Given I have proceeded order with :shippingMethodName shipping method and :paymentMethodName payment
     * @When I proceed with :shippingMethodName shipping method and :paymentMethodName payment
     */
    public function iProceedOrderWithShippingMethodAndPayment($shippingMethodName, $paymentMethodName)
    {
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethodName);
        $this->paymentContext->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @When I proceed through checkout process
     * @When I proceed through checkout process in the :localeCode locale
     */
    public function iProceedThroughCheckoutProcess($localeCode = 'en_US')
    {
        $this->addressingContext->iProceedSelectingShippingCountry(null, $localeCode);
        $this->shippingContext->iCompleteTheShippingStep();
        $this->paymentContext->iCompleteThePaymentStep();
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
     * @When /^I change shipping method to "([^"]*)"$/
     */
    public function iChangeShippingMethod($shippingMethodName)
    {
        $this->paymentContext->iDecideToChangeMyShippingMethod();
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethodName);
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
     * @Then the subtotal of :item item should be :price
     */
    public function theSubtotalOfItemShouldBe($item, $price)
    {
        /** @var AddressPageInterface|SelectPaymentPageInterface|SelectShippingPageInterface|CompletePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->addressPage,
            $this->selectPaymentPage,
            $this->selectShippingPage,
            $this->completePage,
        ]);

        Assert::eq($currentPage->getItemSubtotal($item), $price);
    }
}
