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
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShippingCheckoutContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SelectShippingPageInterface
     */
    private $selectShippingPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SelectShippingPageInterface $selectShippingPage
     */
    public function __construct(SharedStorageInterface $sharedStorage, SelectShippingPageInterface $selectShippingPage)
    {
        $this->sharedStorage = $sharedStorage;
        $this->selectShippingPage = $selectShippingPage;
    }

    /**
     * @Given I have proceeded selecting :shippingMethodName shipping method
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
     * @When I complete the shipping step
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
     * @When /^I proceed selecting ("[^"]+" as shipping country) with "([^"]+)" method$/
     */
    public function iProceedSelectingShippingCountryAndShippingMethod(CountryInterface $shippingCountry = null, $shippingMethodName)
    {
        $this->iProceedSelectingShippingCountry($shippingCountry);

        $this->selectShippingPage->selectShippingMethod($shippingMethodName ?: 'Free');
        $this->selectShippingPage->nextStep();
    }

    /**
     * @When I go back to shipping step of the checkout
     */
    public function iGoBackToShippingStepOfTheCheckout()
    {
        $this->selectShippingPage->open();
    }

    /**
     * @Then I should not be able to select :shippingMethod shipping method
     */
    public function iShouldNotBeAbleToSelectShippingMethod($shippingMethod)
    {
        Assert::false(
            $this->selectShippingPage->hasShippingMethod($shippingMethod),
            sprintf('Shipping method "%s" should not be available but it does.', $shippingMethod)
        );
    }

    /**
     * @Then I should be on the checkout shipping step
     */
    public function iShouldBeOnTheCheckoutShippingStep()
    {
        Assert::true(
            $this->selectShippingPage->isOpen(),
            'Checkout shipping page should be opened, but it is not.'
        );
    }

    /**
     * @Then I should be informed that my order cannot be shipped to this address
     */
    public function iShouldBeInformedThatMyOrderCannotBeShippedToThisAddress()
    {
        Assert::true(
            $this->selectShippingPage->hasNoShippingMethodsMessage(),
            'Shipping page should have no shipping methods message but it does not.'
        );
    }

    /**
     * @Then I should be redirected to the shipping step
     */
    public function iShouldBeRedirectedToTheShippingStep()
    {
        Assert::true(
            $this->selectShippingPage->isOpen(),
            'Checkout shipping step should be opened, but it is not.'
        );
    }

    /**
     * @Then I should see shipping method :shippingMethodName with fee :fee
     */
    public function iShouldSeeShippingFee($shippingMethodName, $fee)
    {
        Assert::true(
            $this->selectShippingPage->hasShippingMethodFee($shippingMethodName, $fee),
            sprintf('The shipping fee should be %s, but it does not.', $fee)
        );
    }
}
