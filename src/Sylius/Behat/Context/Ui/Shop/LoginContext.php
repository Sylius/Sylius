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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\Account\RequestPasswordResetPageInterface;
use Sylius\Behat\Page\Shop\Account\ResetPasswordPageInterface;
use Sylius\Behat\Page\Shop\Account\WellKnownPasswordChangePageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    public function __construct(
        private HomePageInterface $homePage,
        private LoginPageInterface $loginPage,
        private RegisterPageInterface $registerPage,
        private RequestPasswordResetPageInterface $requestPasswordResetPage,
        private ResetPasswordPageInterface $resetPasswordPage,
        private WellKnownPasswordChangePageInterface $wellKnownPasswordChangePage,
        private RegisterElementInterface $registerElement,
        private NotificationCheckerInterface $notificationChecker,
        private CurrentPageResolverInterface $currentPageResolver,
    ) {
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->loginPage->open();
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword(): void
    {
        $this->requestPasswordResetPage->open();
    }

    /**
     * @When I want to reset password from my password manager
     */
    public function iWantToResetPasswordFromMyPasswordManager(): void
    {
        $this->wellKnownPasswordChangePage->tryToOpen();
    }

    /**
     * @When /^I follow link on (my) email to reset my password$/
     */
    public function iFollowLinkOnMyEmailToResetPassword(UserInterface $user): void
    {
        $this->resetPasswordPage->open(['token' => $user->getPasswordResetToken()]);
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername(?string $username = null): void
    {
        $this->loginPage->specifyUsername($username);
    }

    /**
     * @When I specify customer email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail(?string $email = null): void
    {
        $this->requestPasswordResetPage->specifyEmail($email);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs(?string $password = null): void
    {
        $this->loginPage->specifyPassword($password);
    }

    /**
     * @When I specify my new password as :password
     * @When I do not specify my new password
     */
    public function iSpecifyMyNewPassword(?string $password = null): void
    {
        $this->resetPasswordPage->specifyNewPassword($password);
    }

    /**
     * @When I confirm my new password as :password
     * @When I do not confirm my new password
     */
    public function iConfirmMyNewPassword(?string $password = null): void
    {
        $this->resetPasswordPage->specifyConfirmPassword($password);
    }

    /**
     * @When I log in
     * @When I try to log in
     */
    public function iLogIn(): void
    {
        $this->loginPage->logIn();
    }

    /**
     * @When I reset it
     * @When I try to reset it
     */
    public function iResetIt(): void
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
    public function iRegisterWithEmailAndPassword(string $email, string $password): void
    {
        $this->registerPage->open();
        $this->registerElement->specifyEmail($email);
        $this->registerElement->specifyPassword($password);
        $this->registerElement->verifyPassword($password);
        $this->registerElement->specifyFirstName('Carrot');
        $this->registerElement->specifyLastName('Ironfoundersson');
        $this->registerElement->register();
    }

    /**
     * @When I reset password for email :email in :localeCode locale
     */
    public function iResetPasswordForEmailInLocale(string $email, string $localeCode): void
    {
        $this->requestPasswordResetPage->open(['_locale' => $localeCode]);
        $this->iSpecifyTheEmail($email);
        $this->iResetIt();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        $this->homePage->verify();
        Assert::true($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        Assert::false($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::true($this->loginPage->hasValidationErrorWith('Error Invalid credentials.'));
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionWasSent(): void
    {
        $this->notificationChecker->checkNotification(
            'If the email you have specified exists in our system, we have sent there an instruction on how to reset your password.',
            NotificationType::success(),
        );
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
    public function iShouldBeNotifiedThatMyPasswordHasBeenSuccessfullyReset(): void
    {
        $this->notificationChecker->checkNotification('has been reset successfully!', NotificationType::success());
    }

    /**
     * @Then I should be able to log in as :email with :password password
     * @Then the customer should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
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
            'The entered passwords don\'t match',
        ));
    }

    /**
     * @Then I should be notified that the password should be at least :length characters long
     */
    public function iShouldBeNotifiedThatThePasswordShouldBeAtLeastCharactersLong(int $length)
    {
        Assert::true($this->resetPasswordPage->checkValidationMessageFor(
            'password',
            sprintf('Password must be at least %s characters long.', $length),
        ));
    }

    /**
     * @Then I should be redirected to the forgotten password page
     */
    public function iShouldBeRedirectedToTheForgottenPasswordPage()
    {
        Assert::true($this->requestPasswordResetPage->isOpen(), 'User should be on the forgotten password page but they are not.');
    }

    /**
     * @Then I should not be able to change my password again with the same token
     */
    public function iShouldNotBeAbleToChangeMyPasswordAgainWithTheSameToken(): void
    {
        $this->resetPasswordPage->tryToOpen(['token' => 'itotallyforgotmypassword']);

        $this->iShouldNotBeAbleToChangeMyPasswordWithThisToken();
    }

    /**
     * @Then I should not be able to change my password with this token
     * @Then I should not be able to change my password
     */
    public function iShouldNotBeAbleToChangeMyPasswordWithThisToken(): void
    {
        Assert::false($this->resetPasswordPage->isOpen(), 'User should not be on the forgotten password page');
    }
}
