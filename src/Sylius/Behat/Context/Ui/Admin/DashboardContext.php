<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DashboardContext implements Context
{
    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @param DashboardPageInterface $dashboardPage
     */
    public function __construct(DashboardPageInterface $dashboardPage)
    {
        $this->dashboardPage = $dashboardPage;
    }

    /**
     * @When I open administration dashboard
     */
    public function iOpenAdministrationDashboard()
    {
        $this->dashboardPage->open();
    }

    /**
     * @Then I should see :number new orders
     */
    public function iShouldSeeNewOrders($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewOrders(), $number);
    }

    /**
     * @Then I should see :number new customers
     */
    public function iShouldSeeNewCustomers($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewCustomers(), $number);
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
     * @Then I should see :number customers in the list
     */
    public function iShouldSeeCustomersInTheList2($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewCustomersInTheList(), $number);
    }

    /**
     * @Then I should see :number new orders in the list
     */
    public function iShouldSeeNewOrdersInTheList($number)
    {
        Assert::same($this->dashboardPage->getNumberOfNewOrdersInTheList(), $number);
    }
}
