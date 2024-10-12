<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Webmozart\Assert\Assert;

final readonly class CheckoutPaymentContext implements Context
{
    public function __construct(
        private SelectPaymentPageInterface $selectPaymentPage,
        private CompletePageInterface $completePage,
    ) {
    }

    /**
     * @When I try to open checkout payment page
     */
    public function iTryToOpenCheckoutPaymentPage(): void
    {
        $this->selectPaymentPage->tryToOpen();
    }

    /**
     * @When I decide to change order shipping method
     */
    public function iDecideToChangeOrderShippingMethod(): void
    {
        $this->selectPaymentPage->changeShippingMethod();
    }

    /**
     * @Given I completed the payment step with :paymentMethodName payment method
     * @Given the visitor has proceeded with :paymentMethodName payment
     * @Given the customer has proceeded with :paymentMethodName payment
     * @Given the visitor proceed with :paymentMethodName payment
     * @Given the customer proceed with :paymentMethodName payment
     * @When /^I choose "([^"]*)" payment method$/
     */
    public function iChoosePaymentMethod(string $paymentMethodName): void
    {
        $this->selectPaymentPage->selectPaymentMethod($paymentMethodName ?: 'Offline');
        $this->selectPaymentPage->nextStep();
    }

    /**
     * @When I want to pay for order
     */
    public function iWantToPayForOrder(): void
    {
        $this->selectPaymentPage->tryToOpen();
    }

    /**
     * @When I go back to payment step of the checkout
     */
    public function iAmAtTheCheckoutPaymentStep(): void
    {
        $this->selectPaymentPage->open();
    }

    /**
     * @When /^I complete(?:|d) the payment step$/
     */
    public function iCompleteThePaymentStep(): void
    {
        $this->selectPaymentPage->nextStep();
    }

    /**
     * @When I select :paymentMethodName payment method
     */
    public function iSelectPaymentMethod(string $paymentMethodName): void
    {
        $this->selectPaymentPage->selectPaymentMethod($paymentMethodName);
    }

    /**
     * @When I do not select any payment method
     */
    public function iDoNotSelectAnyPaymentMethod(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should be on the checkout payment step
     */
    public function iShouldBeOnTheCheckoutPaymentStep(): void
    {
        $this->selectPaymentPage->verify();
    }

    /**
     * @Then I should be able to select :paymentMethodName payment method
     */
    public function iShouldBeAbleToSelectPaymentMethod(string $paymentMethodName): void
    {
        Assert::true($this->selectPaymentPage->hasPaymentMethod($paymentMethodName));
    }

    /**
     * @Then I should not be able to select :paymentMethodName payment method
     */
    public function iShouldNotBeAbleToSelectPaymentMethod(string $paymentMethodName): void
    {
        Assert::false($this->selectPaymentPage->hasPaymentMethod($paymentMethodName));
    }

    /**
     * @Then I should be redirected to the payment step
     */
    public function iShouldBeRedirectedToThePaymentStep(): void
    {
        $this->selectPaymentPage->verify();
    }

    /**
     * @Then I should be able to go to the summary page again
     */
    public function iShouldBeAbleToGoToTheSummaryPageAgain(): void
    {
        $this->selectPaymentPage->nextStep();

        $this->completePage->verify();
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the first choice
     */
    public function iShouldHavePaymentMethodAvailableAsFirstChoice(string $paymentMethodName): void
    {
        $paymentMethods = $this->selectPaymentPage->getPaymentMethods();

        Assert::same(reset($paymentMethods), $paymentMethodName);
    }

    /**
     * @Then I should have :paymentMethodName payment method available as the last choice
     */
    public function iShouldHavePaymentMethodAvailableAsLastChoice(string $paymentMethodName): void
    {
        $paymentMethods = $this->selectPaymentPage->getPaymentMethods();

        Assert::same(end($paymentMethods), $paymentMethodName);
    }

    /**
     * @Then I should not be able to proceed checkout payment step
     */
    public function iShouldNotBeAbleToProceedCheckoutPaymentStep(): void
    {
        $this->selectPaymentPage->tryToOpen();

        try {
            $this->selectPaymentPage->nextStep();
        } catch (ElementNotFoundException) {
            return;
        }

        throw new UnexpectedPageException('It should not be possible to complete checkout payment step.');
    }

    /**
     * @Then I should see :firstPaymentMethodName and :secondPaymentMethodName payment methods
     */
    public function iShouldSeeAndPaymentMethods(string ...$paymentMethodsNames): void
    {
        foreach ($paymentMethodsNames as $paymentMethodName) {
            Assert::true(
                $this->selectPaymentPage->hasPaymentMethod($paymentMethodName),
                sprintf('There is no %s payment method', $paymentMethodName),
            );
        }
    }

    /**
     * @Then the customer should have checkout payment step completed
     * @Then the visitor should have checkout payment step completed
     */
    public function theCustomerShouldHaveCheckoutPaymentStepCompleted(): void
    {
        Assert::false(
            $this->selectPaymentPage->isOpen(),
            'Customer should have checkout payment step completed, but it is not.',
        );
    }

    /**
     * @Then I should not see :firstPaymentMethodName and :secondPaymentMethodName payment methods
     */
    public function iShouldNotSeeAndPaymentMethods(string ...$paymentMethodsNames): void
    {
        foreach ($paymentMethodsNames as $paymentMethodName) {
            Assert::false(
                $this->selectPaymentPage->hasPaymentMethod($paymentMethodName),
                sprintf('There is %s payment method', $paymentMethodName),
            );
        }
    }

    /**
     * @Then I should not be able to complete the payment step
     */
    public function iShouldNotBeAbleToCompleteThePaymentStep(): void
    {
        Assert::true(
            $this->selectPaymentPage->isNextStepButtonUnavailable(),
            'The "next step" button should be disabled, but it does not.',
        );
    }

    /**
     * @Then there should be information about no payment methods available for my order
     */
    public function thereShouldBeInformationAboutNoPaymentMethodsAvailableForMyOrder(): void
    {
        Assert::true(
            $this->selectPaymentPage->hasNoAvailablePaymentMethodsWarning(),
            'There should be warning about no available payment methods, but it does not.',
        );
    }
}
