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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Webmozart\Assert\Assert;

final class CheckoutPaymentContext implements Context
{
    /**
     * @var SelectPaymentPageInterface
     */
    private $selectPaymentPage;

    /**
     * @var CompletePageInterface
     */
    private $completePage;

    /**
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param CompletePageInterface $completePage
     */
    public function __construct(SelectPaymentPageInterface $selectPaymentPage, CompletePageInterface $completePage)
    {
        $this->selectPaymentPage = $selectPaymentPage;
        $this->completePage = $completePage;
    }

    /**
     * @When I try to open checkout payment page
     */
    public function iTryToOpenCheckoutPaymentPage()
    {
        $this->selectPaymentPage->tryToOpen();
    }

    /**
     * @When I decide to change order shipping method
     */
    public function iDecideToChangeMyShippingMethod()
    {
        $this->selectPaymentPage->changeShippingMethod();
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
     * @When I want to pay for order
     */
    public function iWantToPayForOrder()
    {
        $this->selectPaymentPage->tryToOpen();
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
     * @Then I should be on the checkout payment step
     */
    public function iShouldBeOnTheCheckoutPaymentStep()
    {
        $this->selectPaymentPage->verify();
    }

    /**
     * @Then I should be able to select :paymentMethodName payment method
     */
    public function iShouldBeAbleToSelectPaymentMethod($paymentMethodName)
    {
        Assert::true($this->selectPaymentPage->hasPaymentMethod($paymentMethodName));
    }

    /**
     * @Then I should not be able to select :paymentMethodName payment method
     */
    public function iShouldNotBeAbleToSelectPaymentMethod($paymentMethodName)
    {
        Assert::false($this->selectPaymentPage->hasPaymentMethod($paymentMethodName));
    }

    /**
     * @Then I should be redirected to the payment step
     */
    public function iShouldBeRedirectedToThePaymentStep()
    {
        $this->selectPaymentPage->verify();
    }

    /**
     * @Then I should be able to go to the summary page again
     */
    public function iShouldBeAbleToGoToTheSummaryPageAgain()
    {
        $this->selectPaymentPage->nextStep();

        $this->completePage->verify();
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the first choice
     */
    public function iShouldHavePaymentMethodAvailableAsFirstChoice($paymentMethodName)
    {
        $paymentMethods = $this->selectPaymentPage->getPaymentMethods();

        Assert::same(reset($paymentMethods), $paymentMethodName);
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the last choice
     */
    public function iShouldHavePaymentMethodAvailableAsLastChoice($paymentMethodName)
    {
        $paymentMethods = $this->selectPaymentPage->getPaymentMethods();

        Assert::same(end($paymentMethods), $paymentMethodName);
    }
}
