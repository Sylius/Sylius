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
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ImpersonatingCustomersContext implements Context
{
    /**
     * @var ShowPageInterface
     */
    private $customerShowPage;

    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param ShowPageInterface $customerShowPage
     * @param DashboardPageInterface $dashboardPage
     * @param HomePageInterface $homePage
     */
    public function __construct(
        ShowPageInterface $customerShowPage,
        DashboardPageInterface $dashboardPage,
        HomePageInterface $homePage
    ) {
        $this->customerShowPage = $customerShowPage;
        $this->dashboardPage = $dashboardPage;
        $this->homePage = $homePage;
    }

    /**
     * @Given I am impersonating the customer :customer
     */
    public function iAmImpersonatingCustomer(CustomerInterface $customer)
    {
        $this->customerShowPage->open(['id' => $customer->getId()]);
        $this->customerShowPage->impersonate();
        $this->homePage->open();
    }

    /**
     * @When I visit the store
     */
    public function iVisitTheStore()
    {
        $this->homePage->open();
    }

    /**
     * @When I log out from the store
     */
    public function iLogOut()
    {
        $this->homePage->logOut();
    }

    /**
     * @When I log out from my admin account
     */
    public function iLogOutFromMyAdminAccount()
    {
        $this->dashboardPage->open();
        $this->dashboardPage->logOut();
    }

    /**
     * @When I impersonate them
     */
    public function iTryToImpersonateThem()
    {
        $this->customerShowPage->impersonate();
    }

    /**
     * @Then I should be unable to impersonate them
     */
    public function iShouldBeUnableToImpersonateThem()
    {
        Assert::false($this->customerShowPage->hasImpersonateButton());
    }

    /**
     * @Then I should still be able to access the administration dashboard
     */
    public function iShouldBeAbleToAccessAdministrationDashboard()
    {
        $this->dashboardPage->open();
    }

    /**
     * @Then I should be logged in as :fullName
     */
    public function iShouldBeLoggedInAs($fullName)
    {
        Assert::true($this->homePage->hasLogoutButton());
        Assert::contains($this->homePage->getFullName(), $fullName);
    }

    /**
     * @Then I should not be logged in as :fullName
     */
    public function iShouldNotBeLoggedInAs($fullName)
    {
        $this->homePage->open();

        Assert::false($this->homePage->hasLogoutButton());
        Assert::false(strpos($this->homePage->getFullName(), $fullName));
    }

    /**
     * @Then I should see that impersonating :email was successful
     */
    public function iShouldSeeThatImpersonatingWasSuccessful($email)
    {
        Assert::contains($this->customerShowPage->getSuccessFlashMessage(), $email);
    }
}
