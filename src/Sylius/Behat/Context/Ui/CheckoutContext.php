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
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CheckoutContext implements Context
{
    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SharedSecurityServiceInterface
     */
    private $sharedSecurityService;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param HomePageInterface $homePage
     * @param AddressPageInterface $addressPage
     * @param SelectPaymentPageInterface $selectPaymentPage
     * @param ThankYouPageInterface $thankYouPage
     * @param SelectShippingPageInterface $selectShippingPage
     * @param CompletePageInterface $completePage
     * @param OrderRepositoryInterface $orderRepository
     * @param SharedSecurityServiceInterface $sharedSecurityService
     * @param FactoryInterface $addressFactory
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        HomePageInterface $homePage,
        AddressPageInterface $addressPage,
        SelectPaymentPageInterface $selectPaymentPage,
        ThankYouPageInterface $thankYouPage,
        SelectShippingPageInterface $selectShippingPage,
        CompletePageInterface $completePage,
        OrderRepositoryInterface $orderRepository,
        SharedSecurityServiceInterface $sharedSecurityService,
        FactoryInterface $addressFactory,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->homePage = $homePage;
        $this->addressPage = $addressPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->thankYouPage = $thankYouPage;
        $this->selectShippingPage = $selectShippingPage;
        $this->completePage = $completePage;
        $this->orderRepository = $orderRepository;
        $this->sharedSecurityService = $sharedSecurityService;
        $this->addressFactory = $addressFactory;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I was at the checkout summary step
     */
    public function iWasAtTheCheckoutSummaryStep()
    {
        $this->iSpecifiedTheShippingAddress($this->createDefaultAddress());
        $this->iProceedOrderWithShippingMethodAndPayment('Free', 'Offline');
    }

    /**
     * @Given /^(this user) bought this product$/
     */
    public function thisUserBought(ShopUserInterface $user)
    {
        $this->sharedSecurityService->performActionAsShopUser($user, function () {
            $this->iProceedSelectingPaymentMethod();
            $this->iConfirmMyOrder();
        });
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
     * @When /^I proceed selecting "([^"]+)" shipping method$/
     * @Given /^I chose "([^"]*)" shipping method$/
     */
    public function iProceedSelectingShippingMethod($shippingMethodName)
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod(null, $shippingMethodName);
    }

    /**
     * @When I change payment method to :paymentMethodName
     */
    public function iChangePaymentMethodTo($paymentMethodName)
    {
        $this->thankYouPage->choosePaymentMethod($paymentMethodName);
    }

    /**
     * @When /^I proceed selecting "([^"]*)" as shipping country with "([^"]*)" payment method$/
     */
    public function iProceedSelectingShippingCountryAndPaymentMethod($shippingCountry, $paymentMethodName)
    {
        $this->iProceedSelectingShippingCountryAndShippingMethod($shippingCountry, null);

        $this->iChoosePaymentMethod($paymentMethodName);
    }

    /**
     * @Given I have proceeded selecting :paymentMethodName payment method
     * @When /^I (?:proceed|proceeded) selecting "([^"]+)" payment method$/
     */
    public function iProceedSelectingPaymentMethod($paymentMethodName = 'Offline')
    {
        $this->iProceedSelectingShippingCountryAndPaymentMethod(null, $paymentMethodName);
    }

    /**
     * @Given I should be informed with :paymentMethod payment method instructions
     */
    public function iShouldBeInformedWithPaymentMethodInstructions(PaymentMethodInterface $paymentMethod)
    {
        Assert::same(
            $this->thankYouPage->getInstructions(),
            $paymentMethod->getInstructions()
        );
    }

    /**
     * @Then address to :fullName should be used for both shipping and billing of my order
     */
    public function iShouldSeeThisShippingAddressAsShippingAndBillingAddress($fullName)
    {
        $this->iShouldSeeThisShippingAddressAsShippingAddress($fullName);
        $this->iShouldSeeThisBillingAddressAsBillingAddress($fullName);
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
     * @When I proceed order with :shippingMethod shipping method and :paymentMethod payment
     */
    public function iProceedOrderWithShippingMethodAndPayment($shippingMethod, $paymentMethod)
    {
        $this->iSelectShippingMethod($shippingMethod);
        $this->iCompleteTheShippingStep();
        $this->iSelectPaymentMethod($paymentMethod);
        $this->iCompleteThePaymentStep();
    }

    /**
     * @Then I should be redirected to the homepage
     */
    public function iShouldBeRedirectedToTheHomepage()
    {
        Assert::true(
            $this->homePage->isOpen(),
            'Shop homepage should be opened, but it is not.'
        );
    }

    /**
     * @Given I should be able to go to the payment step again
     */
    public function iShouldBeAbleToGoToThePaymentStepAgain()
    {
        $this->selectShippingPage->nextStep();

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
     * @Given I confirm my changes
     */
    public function iConfirmMyChanges()
    {
        $this->thankYouPage->saveChanges();
    }

    /**
     * @Then the subtotal of :item item should be :price
     */
    public function theSubtotalOfItemShouldBe($item, $price)
    {
        $currentPage = $this->resolveCurrentStepPage();
        $actualPrice = $currentPage->getItemSubtotal($item);

        Assert::eq(
            $actualPrice,
            $price,
            sprintf('The %s subtotal should be %s, but is %s', $item, $price, $actualPrice)
        );
    }

    /**
     * @return SymfonyPageInterface
     */
    private function resolveCurrentStepPage()
    {
        $possiblePages = [
            $this->addressPage,
            $this->selectPaymentPage,
            $this->selectShippingPage,
        ];

        return $this->currentPageResolver->getCurrentPageWithForm($possiblePages);
    }
}
