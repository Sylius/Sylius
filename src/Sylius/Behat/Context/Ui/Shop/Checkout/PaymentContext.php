<?php

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PaymentContext implements Context
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
}
