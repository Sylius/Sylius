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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\Account\RequestPasswordResetPageInterface;
use Sylius\Behat\Page\Shop\Account\ResetPasswordPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    /** @var HomePageInterface */
    private $homePage;

    /** @var LoginPageInterface */
    private $loginPage;

    /** @var RegisterPageInterface */
    private $registerPage;

    /** @var RequestPasswordResetPageInterface */
    private $requestPasswordResetPage;

    /** @var ResetPasswordPageInterface */
    private $resetPasswordPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    public function __construct(
        HomePageInterface $homePage,
        LoginPageInterface $loginPage,
        RegisterPageInterface $registerPage,
        RequestPasswordResetPageInterface $requestPasswordResetPage,
        ResetPasswordPageInterface $resetPasswordPage,
        NotificationCheckerInterface $notificationChecker,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->homePage = $homePage;
        $this->loginPage = $loginPage;
        $this->registerPage = $registerPage;
        $this->requestPasswordResetPage = $requestPasswordResetPage;
        $this->resetPasswordPage = $resetPasswordPage;
        $this->notificationChecker = $notificationChecker;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn()
    {
        $this->loginPage->open();
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword()
    {
        $this->requestPasswordResetPage->open();
    }

    /**
     * @When /^I follow link on (my) email to reset my password$/
     */
    public function iFollowLinkOnMyEmailToResetPassword(UserInterface $user)
    {
        $this->resetPasswordPage->open(['token' => $user->getPasswordResetToken()]);
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername($username = null)
    {
        $this->loginPage->specifyUsername($username);
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $this->requestPasswordResetPage->specifyEmail($email);
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
     * @When I specify my new password as :password
     * @When I do not specify my new password
     */
    public function iSpecifyMyNewPassword(string $password = null)
    {
        $this->resetPasswordPage->specifyNewPassword($password);
    }

    /**
     * @When I confirm my new password as :password
     * @When I do not confirm my new password
     */
    public function iConfirmMyNewPassword(string $password = null)
    {
        $this->resetPasswordPage->specifyConfirmPassword($password);
    }

    /**
     * @When I log in
     * @When I try to log in
     */
    public function iLogIn()
    {
        $this->loginPage->logIn();
    }

    /**
     * @When I reset it
     * @When I try to reset it
     */
    public function iResetIt()
    {
        /** @var RequestPasswordResetPageInterface|ResetPasswordPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->requestPasswordResetPage, $this->resetPasswordPage]);

        $currentPage->reset();
    }

    /**
     * @When I sign in with email :email and password :password
     */
    public function iSignInWithEmailAndPassword(string $email, string $password): void
    {
        $this->iWantToLogIn();
        $this->iSpecifyTheUsername($email);
        $this->iSpecifyThePasswordAs($password);
        $this->iLogIn();
    }

    /**
     * @When I register with email :email and password :password
     */
    public function iRegisterWithEmailAndPassword($email, $password)
    {
        $this->registerPage->open();
        $this->registerPage->specifyEmail($email);
        $this->registerPage->specifyPassword($password);
        $this->registerPage->verifyPassword($password);
        $this->registerPage->specifyFirstName('Carrot');
        $this->registerPage->specifyLastName('Ironfoundersson');
        $this->registerPage->register();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        $this->homePage->verify();
        Assert::true($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        Assert::false($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true($this->loginPage->hasValidationErrorWith('Error Invalid credentials.'));
    }

    /**
     * @Then I should be notified about disabled account
     */
    public function iShouldBeNotifiedAboutDisabledAccount()
    {
        Assert::true($this->loginPage->hasValidationErrorWith('Error Account is disabled.'));
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionWasSent()
    {
        $this->notificationChecker->checkNotification('If the email you have specified exists in our system, we have sent there an instruction on how to reset your password.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the :elementName is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($elementName)
    {
        Assert::true($this->requestPasswordResetPage->checkValidationMessageFor($elementName, sprintf('Please enter your %s.', $elementName)));
    }

    /**
     * @Then I should be notified that my password has been successfully reset
     */
    public function iShouldBeNotifiedThatMyPasswordHasBeenSuccessfullyReset()
    {
        $this->notificationChecker->checkNotification('has been reset successfully!', NotificationType::success());
    }

    /**
     * @Then I should be able to log in as :email with :password password
     * @Then the customer should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword($email, $password)
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();

        $this->iShouldBeLoggedIn();
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch()
    {
        Assert::true($this->resetPasswordPage->checkValidationMessageFor(
            'password',
            'The entered passwords don\'t match'
        ));
    }

    /**
     * @Then I should be notified that the password should be at least 4 characters long
     */
    public function iShouldBeNotifiedThatThePasswordShouldBeAtLeastCharactersLong()
    {
        Assert::true($this->resetPasswordPage->checkValidationMessageFor(
            'password',
            'Password must be at least 4 characters long.'
        ));
    }
}
