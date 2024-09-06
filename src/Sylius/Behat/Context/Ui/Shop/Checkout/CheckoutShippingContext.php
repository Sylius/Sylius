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
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Webmozart\Assert\Assert;

final class CheckoutShippingContext implements Context
{
    public function __construct(
        private SelectShippingPageInterface $selectShippingPage,
        private SelectPaymentPageInterface $selectPaymentPage,
        private CompletePageInterface $completePage,
    ) {
    }

    /**
     * @Given the visitor has proceeded :shippingMethodName shipping method
     * @Given the customer has proceeded :shippingMethodName shipping method
     * @Given the visitor proceed with :shippingMethodName shipping method
     * @Given the customer proceed with :shippingMethodName shipping method
     * @Given I completed the shipping step with :shippingMethodName shipping method
     * @Given I have proceeded with :shippingMethodName shipping method
     * @Given I have proceeded selecting :shippingMethodName shipping method
     * @When I proceed with :shippingMethodName shipping method
     */
    public function iHaveProceededSelectingShippingMethod(string $shippingMethodName): void
    {
        $this->iSelectShippingMethod($shippingMethodName);
        $this->selectShippingPage->nextStep();
    }

    /**
     * @Given I have selected :shippingMethodName shipping method
     * @When I select :shippingMethodName shipping method
     */
    public function iSelectShippingMethod(string $shippingMethodName): void
    {
        $this->selectShippingPage->selectShippingMethod($shippingMethodName);
    }

    /**
     * @When I try to open checkout shipping page
     */
    public function iTryToOpenCheckoutShippingPage(): void
    {
        $this->selectShippingPage->tryToOpen();
    }

    /**
     * @When I try to complete the shipping step
     * @When /^I(?:| try to) complete(?:|d) the shipping step$/
     * @When I complete the shipping step with first shipping method
     */
    public function iCompleteTheShippingStep(): void
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
     * @When I go back to shipping step of the checkout
     */
    public function iGoBackToShippingStepOfTheCheckout()
    {
        $this->selectShippingPage->open();
    }

    /**
     * @Then I should not be able to select :shippingMethodName shipping method
     */
    public function iShouldNotBeAbleToSelectShippingMethod($shippingMethodName)
    {
        Assert::false(in_array($shippingMethodName, $this->selectShippingPage->getShippingMethods(), true));
    }

    /**
     * @Then I should have :shippingMethodName shipping method available as the first choice
     */
    public function iShouldHaveShippingMethodAvailableAsFirstChoice($shippingMethodName)
    {
        $shippingMethods = $this->selectShippingPage->getShippingMethods();

        Assert::same(reset($shippingMethods), $shippingMethodName);
    }

    /**
     * @Then I should have :shippingMethodName shipping method available as the last choice
     */
    public function iShouldHaveShippingMethodAvailableAsLastChoice($shippingMethodName)
    {
        $shippingMethods = $this->selectShippingPage->getShippingMethods();

        Assert::same(end($shippingMethods), $shippingMethodName);
    }

    /**
     * @Then I should be on the checkout shipping step
     * @Then I should be redirected to the shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep()
    {
        $this->selectShippingPage->verify();
    }

    /**
     * @Then I should be informed that my order cannot be shipped to this address
     */
    public function iShouldBeInformedThatMyOrderCannotBeShippedToThisAddress()
    {
        Assert::true($this->selectShippingPage->hasNoShippingMethodsMessage());
    }

    /**
     * @Then I should be able to go to the complete step again
     */
    public function iShouldBeAbleToGoToTheCompleteStepAgain()
    {
        $this->selectShippingPage->nextStep();

        $this->completePage->verify();
    }

    /**
     * @Then I should be able to go to the payment step again
     */
    public function iShouldBeAbleToGoToThePaymentStepAgain()
    {
        $this->selectShippingPage->nextStep();

        $this->selectPaymentPage->verify();
    }

    /**
     * @Then I should see shipping method :shippingMethodName with fee :fee
     */
    public function iShouldSeeShippingFee($shippingMethodName, $fee)
    {
        Assert::true($this->selectShippingPage->hasShippingMethodFee($shippingMethodName, $fee));
    }

    /**
     * @Then there should be information about no available shipping methods
     */
    public function thereShouldBeInformationAboutNoShippingMethodsAvailableForMyShippingAddress()
    {
        Assert::true($this->selectShippingPage->hasNoAvailableShippingMethodsWarning());
    }

    /**
     * @Then I should see :shippingMethodName shipping method
     */
    public function iShouldSeeShippingMethod($shippingMethodName)
    {
        Assert::true($this->selectShippingPage->hasShippingMethod($shippingMethodName));
    }

    /**
     * @Then I should see selected :shippingMethodName shipping method
     */
    public function iShouldSeeSelectedShippingMethod($shippingMethodName)
    {
        Assert::same($this->selectShippingPage->getSelectedShippingMethodName(), $shippingMethodName);
    }

    /**
     * @Then I should not see :shippingMethodName shipping method
     */
    public function iShouldNotSeeShippingMethod($shippingMethodName)
    {
        Assert::false($this->selectShippingPage->hasShippingMethod($shippingMethodName));
    }

    /**
     * @Then I should be checking out as :email
     */
    public function iShouldBeCheckingOutAs($email)
    {
        Assert::same($this->selectShippingPage->getPurchaserIdentifier(), 'Checking out as ' . $email . '.');
    }

    /**
     * @Then the customer should have checkout shipping method step completed
     * @Then the visitor should have checkout shipping method step completed
     */
    public function theCustomerShouldHaveCheckoutShippingMethodStepCompleted()
    {
        Assert::false(
            $this->selectShippingPage->isOpen(),
            'Customer should have checkout shipping method step completed, but it is not.',
        );
    }

    /**
     * @Then I should not be able to proceed checkout shipping step
     */
    public function iShouldNotBeAbleToProceedCheckoutShippingStep(): void
    {
        $this->selectShippingPage->tryToOpen();

        try {
            $this->selectShippingPage->nextStep();
        } catch (ElementNotFoundException) {
            return;
        }

        throw new UnexpectedPageException('It should not be possible to complete checkout shipping step.');
    }
}
