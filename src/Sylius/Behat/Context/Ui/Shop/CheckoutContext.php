<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutAddressingContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutPaymentContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutShippingContext;
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    /** @var AddressPageInterface */
    private $addressPage;

    /** @var SelectPaymentPageInterface */
    private $selectPaymentPage;

    /** @var SelectShippingPageInterface */
    private $selectShippingPage;

    /** @var CompletePageInterface */
    private $completePage;

    /** @var RegisterPageInterface */
    private $registerPage;

    /** @var RegisterElementInterface */
    private $registerElement;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var CheckoutAddressingContext */
    private $addressingContext;

    /** @var CheckoutShippingContext */
    private $shippingContext;

    /** @var CheckoutPaymentContext */
    private $paymentContext;

    public function __construct(
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        RegisterPageInterface $registerPage,
        RegisterElementInterface $registerElement,
        CurrentPageResolverInterface $currentPageResolver,
        CheckoutAddressingContext $addressingContext,
        CheckoutShippingContext $shippingContext,
        CheckoutPaymentContext $paymentContext
    ) {
        $this->addressPage = $addressPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->selectShippingPage = $selectShippingPage;
        $this->completePage = $completePage;
        $this->registerPage = $registerPage;
        $this->registerElement = $registerElement;
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
        $this->addressingContext->iSpecifiedTheBillingAddress();
        $this->iProceedOrderWithShippingMethodAndPayment(null, 'Offline');
    }

    /**
     * @Given I chose :shippingMethod shipping method
     * @When I proceed selecting :shippingMethod shipping method
     */
    public function iProceedSelectingShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->iProceedSelectingBillingCountryAndShippingMethod(null, $shippingMethod);
    }

    /**
     * @Given I have proceeded selecting :paymentMethodName payment method
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingPaymentMethod($paymentMethodName)
    {
        $this->iProceedSelectingBillingCountryAndShippingMethod();
        $this->paymentContext->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @Given I have proceeded order with :shippingMethod shipping method and :paymentMethodName payment
     * @When I proceed with :shippingMethod shipping method and :paymentMethodName payment
     */
    public function iProceedOrderWithShippingMethodAndPayment(?ShippingMethodInterface $shippingMethod, $paymentMethodName)
    {
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethod);
        $this->paymentContext->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @Given I have proceeded through checkout process in the :localeCode locale with email :email
     * @When I proceed through checkout process
     * @When I proceed through checkout process in the :localeCode locale
     * @When I proceed through checkout process in the :localeCode locale with email :email
     */
    public function iProceedThroughCheckoutProcess(string $localeCode = 'en_US', ?string $email = null): void
    {
        $this->addressingContext->iProceedSelectingBillingCountry(null, $localeCode, $email);
        $this->shippingContext->iCompleteTheShippingStep();
        $this->paymentContext->iCompleteThePaymentStep();
    }

    /**
     * @When /^I proceed selecting ("[^"]+" as billing country) with "([^"]+)" method$/
     * @When /^I proceed selecting ("[^"]+" as billing country) with ("[^"]+" shipping method)$/
     */
    public function iProceedSelectingBillingCountryAndShippingMethod(
        CountryInterface $shippingCountry = null,
        ?ShippingMethodInterface $shippingMethod = null
    ): void {
        $this->addressingContext->iProceedSelectingBillingCountry($shippingCountry);
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethod);
    }

    /**
     * @When I change shipping method to :shippingMethod
     */
    public function iChangeShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->paymentContext->iDecideToChangeMyShippingMethod();
        $this->shippingContext->iHaveProceededSelectingShippingMethod($shippingMethod);
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

    /**
     * @When I register with previously used :email email and :password password
     */
    public function iRegisterWithPreviouslyUsedEmailAndPassword(string $email, string $password): void
    {
        $this->registerPage->open();
        $this->registerElement->specifyEmail($email);
        $this->registerElement->specifyPassword($password);
        $this->registerElement->verifyPassword($password);
        $this->registerElement->specifyFirstName('Carrot');
        $this->registerElement->specifyLastName('Ironfoundersson');
        $this->registerElement->register();
    }
}
