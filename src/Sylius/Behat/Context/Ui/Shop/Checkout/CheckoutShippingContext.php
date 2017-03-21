<?php

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CheckoutShippingContext implements Context
{
    /**
     * @var SelectShippingPageInterface
     */
    private $selectShippingPage;

    /**
     * @var SelectPaymentPageInterface
     */
    private $selectPaymentPage;

    /**
     * @var CompletePageInterface
     */
    private $completePage;

    /**
     * @param SelectShippingPageInterface $selectShippingPage
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param CompletePageInterface $completePage
     */
    public function __construct(
        SelectShippingPageInterface $selectShippingPage,
        SelectPaymentPageInterface $selectPaymentPage,
        CompletePageInterface $completePage
    ) {
        $this->selectShippingPage = $selectShippingPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->completePage = $completePage;
    }

    /**
     * @Given I have proceeded selecting :shippingMethodName shipping method
     * @When I proceed with :shippingMethodName shipping method
     */
    public function iHaveProceededSelectingShippingMethod($shippingMethodName)
    {
        $this->iSelectShippingMethod($shippingMethodName);
        $this->selectShippingPage->nextStep();
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
     * @When I try to open checkout shipping page
     */
    public function iTryToOpenCheckoutShippingPage()
    {
        $this->selectShippingPage->tryToOpen();
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
        Assert::same($this->selectShippingPage->getPurchaserEmail(), 'Checking out as '.$email.'.');
    }
}
