<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /**
     * @var PaypalExpressCheckoutPageInterface
     */
    private $paypalExpressCheckoutPage;

    /**
     * @var ShowPageInterface
     */
    private $orderDetails;

    /**
     * @var CompletePageInterface
     */
    private $summaryPage;

    /**
     * @var PaypalApiMocker
     */
    private $paypalApiMocker;

    /**
     * @param PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage
     * @param ShowPageInterface $orderDetails
     * @param CompletePageInterface $summaryPage
     * @param PaypalApiMocker $paypalApiMocker
     */
    public function __construct(
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        ShowPageInterface $orderDetails,
        CompletePageInterface $summaryPage,
        PaypalApiMocker $paypalApiMocker
    ) {
        $this->paypalExpressCheckoutPage = $paypalExpressCheckoutPage;
        $this->orderDetails = $orderDetails;
        $this->summaryPage = $summaryPage;
        $this->paypalApiMocker = $paypalApiMocker;
    }

    /**
     * @When /^I confirm my order with paypal payment$/
     * @Given /^I have confirmed my order with paypal payment$/
     */
    public function iConfirmMyOrderWithPaypalPayment(): void
    {
        $this->paypalApiMocker->performActionInApiInitializeScope(function (): void {
            $this->summaryPage->confirmOrder();
        });
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully(): void
    {
        $this->paypalApiMocker->performActionInApiSuccessfulScope(function (): void {
            $this->paypalExpressCheckoutPage->pay();
        });
    }

    /**
     * @Given /^I have cancelled (?:|my )PayPal payment$/
     * @When /^I cancel (?:|my )PayPal payment$/
     */
    public function iCancelMyPaypalPayment(): void
    {
        $this->paypalExpressCheckoutPage->cancel();
    }

    /**
     * @When /^I try to pay(?:| again)$/
     */
    public function iTryToPayAgain(): void
    {
        $this->paypalApiMocker->performActionInApiInitializeScope(function (): void {
            $this->orderDetails->pay();
        });
    }

    /**
     * @Then I should be notified that my payment has been cancelled
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCancelled(): void
    {
        $this->assertNotification('Payment has been cancelled.');
    }

    /**
     * @Then I should be notified that my payment has been completed
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCompleted(): void
    {
        $this->assertNotification('Payment has been completed.');
    }

    /**
     * @param string $expectedNotification
     */
    private function assertNotification(string $expectedNotification): void
    {
        $notifications = $this->orderDetails->getNotifications();
        $hasNotifications = '';

        foreach ($notifications as $notification) {
            $hasNotifications .= $notification;
            if ($notification === $expectedNotification) {
                return;
            }
        }

        throw new \RuntimeException(sprintf('There is no notificaiton with "%s". Got "%s"', $expectedNotification, $hasNotifications));
    }
}
