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
use Behat\Step\Then;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class DashboardContext implements Context
{
    public function __construct(private DashboardPageInterface $dashboardPage)
    {
    }

    /**
     * Retry assertion until value matches or timeout.
     * For Live Component updates where value may be stale even when element exists.
     */
    private function assertWithRetry(callable $getter, mixed $expected, int $maxAttempts = 3, int $waitMs = 500): void
    {
        $lastActual = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $lastActual = $getter();

            if ($lastActual === $expected) {
                Assert::same($lastActual, $expected); // Pass
                return;
            }

            // Not matching yet - wait before retry (except on last attempt)
            if ($attempt < $maxAttempts) {
                usleep($waitMs * 1000);
            }
        }

        // All attempts failed - throw assertion with last value
        Assert::same($lastActual, $expected);
    }

    /**
     * @Given I am on the administration dashboard
     * @When I (try to )open administration dashboard
     * @When I (try to )view statistics
     */
    public function iViewStatistics(): void
    {
        try {
            $this->dashboardPage->open();
        } catch (UnexpectedPageException) {
        }
    }

    /**
     * @When I view statistics for :channel channel
     *
     * @throws UnexpectedPageException
     */
    public function iViewStatisticsForChannel(ChannelInterface $channel): void
    {
        $this->dashboardPage->open(['channel' => $channel->getCode()]);
    }

    /**
     * @When /^I view statistics for ("[^"]+" channel) and (current|previous|next) year split by (month|day)$/
     *
     * @throws UnexpectedPageException
     */
    public function iViewStatisticsForChannelAndYear(
        ChannelInterface $channel,
        string $period,
        string $interval,
    ): void {
        if (!$this->dashboardPage->isOpen(['channel' => $channel->getCode()])) {
            $this->dashboardPage->open(['channel' => $channel->getCode()]);
        }

        match ($interval) {
            'month' => $this->dashboardPage->chooseYearSplitByMonthsInterval(),
            'day' => $this->dashboardPage->chooseMonthSplitByDaysInterval(),
            default => throw new \InvalidArgumentException(sprintf('Interval "%s" is not supported.', $interval)),
        };

        match ($period) {
            'previous' => $this->dashboardPage->choosePreviousPeriod(),
            'next' => $this->dashboardPage->chooseNextPeriod(),
            default => null,
        };
    }

    /**
     * @When /^I view statistics for ("[^"]+" channel) and (previous|next) year$/
     *
     * @throws UnexpectedPageException
     */
    public function iViewStatisticsForPreviousPeriod(
        ChannelInterface $channel,
        string $period,
    ): void {
        if (!$this->dashboardPage->isOpen(['channel' => $channel->getCode()])) {
            $this->dashboardPage->open(['channel' => $channel->getCode()]);
        }

        match ($period) {
            'previous' => $this->dashboardPage->choosePreviousPeriod(),
            'next' => $this->dashboardPage->chooseNextPeriod(),
            default => null,
        };
    }

    /**
     * @When I choose :channelName channel
     */
    public function iChooseChannel(string $channelName): void
    {
        $this->dashboardPage->chooseChannel($channelName);
    }

    /**
     * @When I search for product :product via the navbar
     */
    public function iSearchForProductViaTheNavbar(ProductInterface $product): void
    {
        $this->dashboardPage->searchForProductViaNavbar($product);
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->dashboardPage->logOut();
    }

    /**
     * @Then I should see :number paid orders
     */
    public function iShouldSeePaidOrders(int $number): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfPaidOrders(),
            $number
        );
    }

    /**
     * @Then I should see :number new customers
     */
    public function iShouldSeeNewCustomers(int $number): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfNewCustomers(),
            $number
        );
    }

    /**
     * @Then there should be total sales of :total
     */
    public function thereShouldBeTotalSalesOf(string $total): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getTotalSales(),
            $total
        );
    }

    /**
     * @Then the average order value should be :value
     */
    public function myAverageOrderValueShouldBe(string $value): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getAverageOrderValue(),
            $value
        );
    }

    /**
     * @Then I should see :number new customers in the list
     */
    public function iShouldSeeNewCustomersInTheList(int $number): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfNewCustomersInTheList(),
            $number
        );
    }

    /**
     * @Then I should see :number new orders in the list
     */
    public function iShouldSeeNewOrdersInTheList(int $number): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfNewOrdersInTheList(),
            $number
        );
    }

    /**
     * @Then I should not see the administration dashboard
     */
    public function iShouldNotSeeTheAdministrationDashboard(): void
    {
        Assert::false($this->dashboardPage->isOpen());
    }

    #[Then('I should see :count order(s) to process in the pending actions')]
    public function iShouldSeeOrdersToProcessInThePendingActions(int $count): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfOrdersToProcess(),
            $count
        );
    }

    #[Then('I should see :count shipment(s) to ship in the pending actions')]
    public function iShouldSeeShipmentsToShipInThePendingActions(int $count): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfShipmentsToShip(),
            $count
        );
    }

    #[Then('I should see :count pending payment(s) in the pending actions')]
    public function iShouldSeePendingPaymentsInThePendingActions(int $count): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfPendingPayments(),
            $count
        );
    }

    #[Then('I should see :count product review(s) to approve in the pending actions')]
    public function iShouldSeeProductReviewsToApproveInThePendingActions(int $count): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfProductReviewsToApprove(),
            $count
        );
    }

    #[Then('I should see :count product variant(s) out of stock in the pending actions')]
    public function iShouldSeeProductVariantsOutOfStockInThePendingActions(int $count): void
    {
        $this->assertWithRetry(
            fn() => $this->dashboardPage->getNumberOfProductVariantsOutOfStock(),
            $count
        );
    }
}
