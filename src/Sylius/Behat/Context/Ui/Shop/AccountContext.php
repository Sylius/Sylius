<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\ProfileUpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\ShopHomePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AccountContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var ProfileUpdatePageInterface
     */
    private $profileUpdatePage;

    /**
     * @var LoginPageInterface
     */
    private $loginPage;

    /**
     * @var RegisterPageInterface
     */
    private $registerPage;

    /**
     * @var ShopHomePageInterface
     */
    private $shopHomePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     * @param DashboardPageInterface $dashboardPage
     * @param ProfileUpdatePageInterface $profileUpdatePage
     * @param LoginPageInterface $loginPage
     * @param RegisterPageInterface $registerPage
     * @param ShopHomePageInterface $shopHomePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        DashboardPageInterface $dashboardPage,
        ProfileUpdatePageInterface $profileUpdatePage,
        LoginPageInterface $loginPage,
        RegisterPageInterface $registerPage,
        ShopHomePageInterface $shopHomePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->dashboardPage = $dashboardPage;
        $this->profileUpdatePage = $profileUpdatePage;
        $this->loginPage = $loginPage;
        $this->registerPage = $registerPage;
        $this->shopHomePage = $shopHomePage;
    }

    /**
     * @Given I want to modify my profile
     */
    public function iWantToModifyMyProfile()
    {
        $this->profileUpdatePage->open();
    }

    /**
     * @Given I want to log in
     */
    public function iWantToLogIn()
    {
        $this->loginPage->open();
    }

    /**
     * @Given I want to register a new account
     */
    public function iWantToRegisterANewAccount()
    {
        $this->registerPage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName($firstName = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->profileUpdatePage, $this->registerPage]);
        $currentPage->specifyFirstName($firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName($lastName = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->profileUpdatePage, $this->registerPage]);
        $currentPage->specifyLastName($lastName);
    }

    /**
     * @When I specify the email as :email
     * @When I remove the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->profileUpdatePage, $this->registerPage]);
        $currentPage->specifyEmail($email);
    }

    /**
     * @When I specify password with :password
     * @When I do not specify password
     */
    public function iSpecifyPasswordWith($password = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->loginPage, $this->registerPage]);
        $currentPage->specifyPassword($password);
        $this->sharedStorage->set('password', $password);
    }

    /**
     * @When /^I confirm (this password)$/
     */
    public function iConfirmThisPassword($password)
    {
        $this->registerPage->verifyPassword($password);
    }

    /**
     * @When I do not confirm password
     */
    public function iDoNotConfirmPassword()
    {
        $this->registerPage->verifyPassword(null);
    }

    /**
     * @When I specify phone number with :phoneNumber
     */
    public function iSpecifyPhoneNumberWith($phoneNumber)
    {
        $this->registerPage->specifyPhoneNumber($phoneNumber);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->profileUpdatePage->saveChanges();
    }

    /**
     * @When I log in
     */
    public function iLogIn()
    {
        $this->loginPage->logIn();
    }

    /**
     * @When I register this account
     * @When I try to register this account
     */
    public function iRegister()
    {
        $this->registerPage->register();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited()
    {
        $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then my name should be :name
     * @Then my name should still be :name
     */
    public function myNameShouldBe($name)
    {
        $this->dashboardPage->open();

        Assert::true(
            $this->dashboardPage->hasCustomerName($name),
            sprintf('Cannot find customer name "%s".', $name)
        );
    }

    /**
     * @Then my email should be :email
     * @Then my email should still be :email
     */
    public function myEmailShouldBe($email)
    {
        $this->dashboardPage->open();

        Assert::true(
            $this->dashboardPage->hasCustomerEmail($email),
            sprintf('Cannot find customer email "%s".', $email)
        );
    }

    /**
     * @Then I should be notified that new account has been successfully created
     */
    public function iShouldBeNotifiedThatNewAccountHasBeenSuccessfullyCreated()
    {
        $this->notificationChecker->checkNotification('Customer has been successfully created.', NotificationType::success());
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        Assert::true(
            $this->shopHomePage->hasLogoutButton(),
            'I should be on home page and, also i should be able to sign out.'
        );
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        Assert::false(
            $this->shopHomePage->hasLogoutButton(),
            'I should not be logged in.'
        );
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true(
            $this->loginPage->hasValidationErrorWith('Error Invalid credentials.'),
            'I should see validation error.'
        );
    }

    /**
     * @Then /^I should be notified that the ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter your %s.', $element));
    }

    /**
     * @Then /^I should be notified that the ([^"]+) is invalid$/
     */
    public function iShouldBeNotifiedThatElementIsInvalid($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('This %s is invalid.', $element));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed()
    {
        $this->assertFieldValidationMessage('email', 'This email is already used.');
    }

    /**
     * @Then I should be notified that password do not match
     */
    public function iShouldBeNotifiedThatPasswordDoNotMatch()
    {
        $this->assertFieldValidationMessage('password', "The entered passwords don't match");
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->profileUpdatePage, $this->registerPage);
        Assert::true(
            $currentPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('The %s should be required.', $element)
        );
    }
}
