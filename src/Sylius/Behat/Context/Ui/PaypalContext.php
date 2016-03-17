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
use Sylius\Behat\Page\Shop\Checkout\FinalizeStepInterface;
use Sylius\Behat\Page\External\PaypalExpressCheckoutPageInterface;
use Sylius\Behat\Page\Shop\Order\OrderPaymentsPageInterface;
use Sylius\Behat\Service\Mocker\PaypalApiMocker;
use Sylius\Component\Core\Model\OrderInterface;
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
     * @var OrderPaymentsPageInterface
     */
    private $orderPaymentsPage;

    /**
     * @var PaypalExpressCheckoutPageInterface
     */
    private $paypalExpressCheckoutPage;

    /**
     * @var FinalizeStepInterface
     */
    private $checkoutFinalizeStep;

    /**
     * @var PaypalApiMocker
     */
    private $paypalApiMocker;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OrderPaymentsPageInterface $orderPaymentsPage
     * @param PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage
     * @param FinalizeStepInterface $checkoutFinalizeStep
     * @param PaypalApiMocker $paypalApiMocker
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderPaymentsPageInterface $orderPaymentsPage,
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        FinalizeStepInterface $checkoutFinalizeStep,
        PaypalApiMocker $paypalApiMocker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderPaymentsPage = $orderPaymentsPage;
        $this->paypalExpressCheckoutPage = $paypalExpressCheckoutPage;
        $this->checkoutFinalizeStep = $checkoutFinalizeStep;
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
     * @Then I should be redirected to PayPal Express Checkout page
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
        $order = $this->getLastOrder();
        $payment = $order->getLastPayment();
        $this->paypalApiMocker->mockApiPaymentInitializeResponse();
        $this->orderPaymentsPage->clickPayButtonForGivenPayment($payment);
    }

    /**
     * @return OrderInterface
     *
     * @throws \RuntimeException
     */
    private function getLastOrder()
    {
        $customer = $this->sharedStorage->get('user')->getCustomer();
        $orders = $this->orderRepository->findByCustomer($customer);
        $lastOrder = end($orders);

        if (false === $lastOrder) {
            throw new \RuntimeException(sprintf('There is no last order for %s', $customer->getFullName()));
        }

        return $lastOrder;
    }
}
