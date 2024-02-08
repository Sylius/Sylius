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
use Sylius\Component\Core\Formatter\StringInflector;
use Webmozart\Assert\Assert;

final class DashboardContext implements Context
{
    public function __construct(private DashboardPageInterface $dashboardPage)
    {
    }

    /**
     * @Given I am on the administration dashboard
     * @When I (try to )open administration dashboard
     */
    public function iOpenAdministrationDashboard(): void
    {
        try {
            $this->dashboardPage->open();
        } catch (UnexpectedPageException) {
        }
    }

    /**
     * @When I open administration dashboard for :name channel
     */
    public function iOpenAdministrationDashboardForChannel($name)
    {
        $this->dashboardPage->open(['channel' => StringInflector::nameToLowercaseCode($name)]);
    }

    /**
     * @When I choose :channelName channel
     */
    public function iChooseChannel($channelName)
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
     */
    public function iShouldSeeNewOrders($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewOrders(), (int) $number);
    }

    /**
     * @Then I should see :number new customers
     */
    public function iShouldSeeNewCustomers($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewCustomers(), (int) $number);
    }

    /**
     * @Then there should be total sales of :total
     */
    public function thereShouldBeTotalSalesOf($total)
    {
        Assert::same($this->dashboardPage->getTotalSales(), $total);
    }

    /**
     * @Then the average order value should be :value
     */
    public function myAverageOrderValueShouldBe($value)
    {
        Assert::same($this->dashboardPage->getAverageOrderValue(), $value);
    }

    /**
     * @Then I should see :number new customers in the list
     */
    public function iShouldSeeNewCustomersInTheList($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewCustomersInTheList(), (int) $number);
    }

    /**
     * @Then I should see :number new orders in the list
     */
    public function iShouldSeeNewOrdersInTheList($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewOrdersInTheList(), (int) $number);
    }

    /**
     * @Then I should not see the administration dashboard
     */
    public function iShouldNotSeeTheAdministrationDashboard(): void
    {
        Assert::false($this->dashboardPage->isOpen());
    }
}
