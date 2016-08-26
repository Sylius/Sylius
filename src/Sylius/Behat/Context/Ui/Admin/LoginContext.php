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
use Sylius\Behat\Page\Admin\Account\LoginPageInterface;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class LoginContext implements Context
{
    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var LoginPageInterface
     */
    private $loginPage;

    /**
     * @param DashboardPageInterface $dashboardPage
     * @param LoginPageInterface $loginPage
     */
    public function __construct(DashboardPageInterface $dashboardPage, LoginPageInterface $loginPage)
    {
        $this->dashboardPage = $dashboardPage;
        $this->loginPage = $loginPage;
    }

    /**
     * @Given I want to log in
     */
    public function iWantToLogIn()
    {
        $this->loginPage->open();
    }

    /**
     * @When I specify the username as :username
     * @When I do not specify the user name
     */
    public function iSpecifyTheUsername($username = null)
    {
        $this->loginPage->specifyUsername($username);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs($password = null)
    {
        $this->loginPage->specifyPassword($password);
    }

    /**
     * @When I log in
     */
    public function iLogIn()
    {
        $this->loginPage->logIn();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        Assert::true(
            $this->dashboardPage->isOpen(),
            'I should be on administration dashboard page.'
        );
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        Assert::false(
            $this->dashboardPage->isOpen(),
            'I should not have access to administration dashboard page.'
        );
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true(
            $this->loginPage->hasValidationErrorWith('Error Bad credentials.'),
            'I should see validation error.'
        );
    }

    /**
     * @Then I should be able to log in as :username authenticated by :password password
     */
    public function iShouldBeAbleToLogInAsAuthenticatedByPassword($username, $password)
    {
        $this->logInAgain($username, $password);

        Assert::true(
            $this->dashboardPage->isOpen(),
            'I should be able to log in.'
        );
    }

    /**
     * @Then I should not be able to log in as :username authenticated by :password password
     */
    public function iShouldNotBeAbleToLogInAsAuthenticatedByPassword($username, $password)
    {
        $this->logInAgain($username, $password);

        Assert::true(
            $this->loginPage->hasValidationErrorWith('Error Bad credentials.'),
            'I should see validation error.'
        );

        Assert::false(
            $this->dashboardPage->isOpen(),
            'I should not be able to log in.'
        );
    }

    /**
     * @param string $username
     * @param string $password
     */
    private function logInAgain($username, $password)
    {
        $this->dashboardPage->logOut();
        $this->loginPage->open();
        $this->loginPage->specifyUsername($username);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();
    }
}
