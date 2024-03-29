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
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class DashboardContext implements Context
{
    public function __construct(private DashboardPageInterface $dashboardPage)
    {
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
        $this->dashboardPage->open(['channel' => $channel->getCode()]);

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
     * @When I choose :channelName channel
     */
    public function iChooseChannel(string $channelName): void
    {
        $this->dashboardPage->chooseChannel($channelName);
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->dashboardPage->logOut();
    }

    /**
     * @Then I should see :number new orders
     * @Then I should see :number paid orders
     */
    public function iShouldSeeNewOrders(int $number): void
    {
        Assert::same($this->dashboardPage->getNumberOfNewOrders(), $number);
    }

    /**
     * @Then I should see :number new customers
     */
    public function iShouldSeeNewCustomers(int $number): void
    {
        Assert::same($this->dashboardPage->getNumberOfNewCustomers(), $number);
    }

    /**
     * @Then there should be total sales of :total
     */
    public function thereShouldBeTotalSalesOf(string $total): void
    {
        Assert::same($this->dashboardPage->getTotalSales(), $total);
    }

    /**
     * @Then the average order value should be :value
     */
    public function myAverageOrderValueShouldBe(string $value): void
    {
        Assert::same(
            $this->dashboardPage->getAverageOrderValue(),
            $value,
            'Expected average order value to be equal to %2$s, but it is %s.',
        );
    }

    /**
     * @Then I should see :number new customers in the list
     */
    public function iShouldSeeNewCustomersInTheList(int $number): void
    {
        Assert::same($this->dashboardPage->getNumberOfNewCustomersInTheList(), $number);
    }

    /**
     * @Then I should see :number new orders in the list
     */
    public function iShouldSeeNewOrdersInTheList(int $number): void
    {
        Assert::same($this->dashboardPage->getNumberOfNewOrdersInTheList(), $number);
    }

    /**
     * @Then I should not see the administration dashboard
     */
    public function iShouldNotSeeTheAdministrationDashboard(): void
    {
        Assert::false($this->dashboardPage->isOpen());
    }
}
