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
use Sylius\Behat\Page\Shop\Checkout\CanceledPaymentPageInterface;
use Sylius\Behat\Page\Shop\Checkout\FinalizeStepInterface;
use Sylius\Behat\Page\External\PaypalExpressCheckoutPageInterface;
use Sylius\Behat\Page\Shop\Checkout\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Checkout\ThankYouPageInterface;
use Sylius\Behat\Service\Mocker\PaypalApiMocker;
use Sylius\Behat\Service\Mocker\PaypalApiMockerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaypalContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PaypalExpressCheckoutPageInterface
     */
    private $paypalExpressCheckoutPage;

    /**
     * @var ThankYouPageInterface
     */
    private $thankYouPage;
    
    /**
     * @var FinalizeStepInterface
     */
    private $checkoutFinalizeStep;

    /**
     * @var PaypalApiMockerInterface
     */
    private $paypalApiMocker;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage
     * @param ThankYouPageInterface $thankYouPage
     * @param FinalizeStepInterface $checkoutFinalizeStep
     * @param PaypalApiMocker $paypalApiMocker
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        ThankYouPageInterface $thankYouPage,
        FinalizeStepInterface $checkoutFinalizeStep,
        PaypalApiMocker $paypalApiMocker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paypalExpressCheckoutPage = $paypalExpressCheckoutPage;
        $this->thankYouPage = $thankYouPage;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
        $this->paypalApiMocker = $paypalApiMocker;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Then I should be redirected back to PayPal Express Checkout page
     */
    public function iShouldBeRedirectedToPaypalExpressCheckoutPage()
    {
        expect($this->paypalExpressCheckoutPage->isOpen())->toBe(true);
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully()
    {
        $this->paypalApiMocker->mockApiSuccessfulPaymentResponse();
        $this->paypalExpressCheckoutPage->pay();
    }

    /**
     * @Given I cancel my PayPal payment
     */
    public function iCancelMyPaypalPayment()
    {
        $this->paypalExpressCheckoutPage->cancel();
    }

    /**
     * @When /^I try to pay(?: again|)$/
     */
    public function iTryToPayAgain()
    {
        $this->paypalApiMocker->mockApiPaymentInitializeResponse();
        $this->thankYouPage->pay();
    }
}
