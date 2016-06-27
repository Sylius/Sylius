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
     * @var FinalizeStepInterface
     */
    private $checkoutFinalizeStep;

    /**
     * @var CanceledPaymentPageInterface
     */
    private $canceledPaymentPage;

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
     * @param FinalizeStepInterface $checkoutFinalizeStep
     * @param CanceledPaymentPageInterface $canceledPaymentPage
     * @param PaypalApiMocker $paypalApiMocker
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        FinalizeStepInterface $checkoutFinalizeStep,
        CanceledPaymentPageInterface $canceledPaymentPage,
        PaypalApiMocker $paypalApiMocker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paypalExpressCheckoutPage = $paypalExpressCheckoutPage;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
        $this->canceledPaymentPage = $canceledPaymentPage;
        $this->paypalApiMocker = $paypalApiMocker;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given /^I confirm my order with paypal payment$/
     */
    public function iConfirmMyOrderWithPaypalPayment()
    {
        $this->paypalApiMocker->mockApiPaymentInitializeResponse();
        $this->checkoutFinalizeStep->confirmOrder();
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
     * @When I cancel my PayPal payment
     */
    public function iCancelMyPaypalPayment()
    {
        $this->paypalExpressCheckoutPage->cancel();
    }

    /**
     * @When I try to pay again
     */
    public function iTryToPayAgain()
    {
        $this->paypalApiMocker->mockApiPaymentInitializeResponse();
        $this->canceledPaymentPage->clickPayButton();
    }
}
