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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Page\Admin\Payment\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class ManagingPaymentsContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private ShowPageInterface $orderShowPage,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @Given I am browsing payments
     * @When I browse payments
     */
    public function iAmBrowsingPayments(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I choose :paymentState as a payment state
     */
    public function iChooseAsAPaymentState(string $paymentState): void
    {
        $this->indexPage->chooseStateToFilter($paymentState);
    }

    /**
     * @When I complete the payment of order :orderNumber
     */
    public function iCompleteThePaymentOfOrder(string $orderNumber): void
    {
        $this->indexPage->completePaymentOfOrderWithNumber($orderNumber);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @When I go to the details of the first payment's order
     */
    public function iGoToTheDetailsOfTheFirstPaymentSOrder(): void
    {
        $this->indexPage->showOrderPageForNthPayment(1);
    }

    /**
     * @When I choose :channelName as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(string $channelName): void
    {
        $this->indexPage->chooseChannelFilter($channelName);
    }

    /**
     * @Then I should see :count payments in the list
     * @Then I should see a single payment in the list
     */
    public function iShouldSeePaymentsInTheList(int $count = 1): void
    {
        Assert::same($count, $this->indexPage->countItems());
    }

    /**
     * @Then the payment of the :orderNumber order should be :paymentState for :customer
     */
    public function thePaymentOfTheOrderShouldBeFor(
        string $orderNumber,
        string $paymentState,
        CustomerInterface $customer,
    ): void {
        $parameters = [
            'number' => $orderNumber,
            'state' => $paymentState,
            'customer' => $customer->getEmail(),
        ];

        Assert::true($this->indexPage->isSingleResourceOnPage($parameters));
    }

    /**
     * @Then I should see order page with details of order :order
     * @Then I should see the details of order :order
     */
    public function iShouldSeeOrderPageWithDetailsOfOrder(OrderInterface $order): void
    {
        Assert::true($this->orderShowPage->isOpen(['id' => $order->getId()]));
    }

    /**
     * @Then I should see (also) the payment of the :orderNumber order
     */
    public function iShouldSeeThePaymentOfTheOrder(string $orderNumber): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should see the payment of order :orderNumber as :paymentState
     */
    public function iShouldSeeThePaymentOfOrderAs(string $orderNumber, string $paymentState): void
    {
        Assert::same($paymentState, $this->indexPage->getPaymentStateByOrderNumber($orderNumber));
    }

    /**
     * @Then I should be notified that the payment has been completed
     */
    public function iShouldBeNotifiedThatThePaymentHasBeenCompleted(): void
    {
        $this->notificationChecker->checkNotification('Payment has been completed.', NotificationType::success());
    }

    /**
     * @Then I should not see a payment of order :orderNumber
     * @Then I should not see the payment of the :orderNumber order
     */
    public function iShouldNotSeeAPaymentOfOrder(string $orderNumber): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then /^I should see payment for (the "[^"]+" order) as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldSeePaymentForTheOrderInTheList(string $orderNumber, int $position): void
    {
        Assert::true($this->indexPage->isPaymentWithOrderNumberInPosition($orderNumber, $position));
    }
}
