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

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\External\PaypalExpressCheckoutPageInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Behat\Service\Mocker\PaypalApiMocker;

final class PaypalContext implements Context
{
    public function __construct(
        private PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        private ShowPageInterface $orderDetails,
        private CompletePageInterface $summaryPage,
        private PaypalApiMocker $paypalApiMocker,
    ) {
    }

    /**
     * @Given /^I have confirmed my order with paypal payment$/
     * @When /^I confirm my order with paypal payment$/
     */
    public function iConfirmMyOrderWithPaypalPayment()
    {
        $this->paypalApiMocker->performActionInApiInitializeScope(function () {
            $this->summaryPage->confirmOrder();
        });
    }

    /**
     * @When I sign in to PayPal and authorize successfully
     */
    public function iSignInToPaypalAndAuthorizeSuccessfully()
    {
        $this->paypalApiMocker->performActionInApiSuccessfulScope(function () {
            $this->paypalExpressCheckoutPage->authorize();
        });
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully()
    {
        $this->paypalApiMocker->performActionInApiSuccessfulScope(function () {
            $this->paypalExpressCheckoutPage->pay();
        });
    }

    /**
     * @Given /^I have cancelled (?:|my )PayPal payment$/
     * @When /^I cancel (?:|my )PayPal payment$/
     */
    public function iCancelMyPaypalPayment()
    {
        $this->paypalExpressCheckoutPage->cancel();
    }

    /**
     * @When /^I try to pay(?:| again)$/
     */
    public function iTryToPayAgain()
    {
        $this->paypalApiMocker->performActionInApiInitializeScope(function () {
            $this->orderDetails->pay();
        });
    }

    /**
     * @Then I should be notified that my payment has been cancelled
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCancelled()
    {
        $this->assertNotification('Payment has been cancelled.');
    }

    /**
     * @Then I should be notified that my payment has been completed
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCompleted()
    {
        $this->assertNotification('Payment has been completed.');
    }

    /**
     * @param string $expectedNotification
     */
    private function assertNotification($expectedNotification)
    {
        $notifications = $this->orderDetails->getNotifications();
        $hasNotifications = '';

        foreach ($notifications as $notification) {
            $hasNotifications .= $notification;
            if ($notification === $expectedNotification) {
                return;
            }
        }

        throw new \RuntimeException(sprintf('There is no notification with "%s". Got "%s"', $expectedNotification, $hasNotifications));
    }
}
