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
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\ProfileUpdatePageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\Account\VerificationPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webmozart\Assert\Assert;

class RegistrationContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var DashboardPageInterface */
    private $dashboardPage;

    /** @var HomePageInterface */
    private $homePage;

    /** @var LoginPageInterface */
    private $loginPage;

    /** @var RegisterPageInterface */
    private $registerPage;

    /** @var VerificationPageInterface */
    private $verificationPage;

    /** @var ProfileUpdatePageInterface */
    private $profileUpdatePage;

    /** @var RegisterElementInterface */
    private $registerElement;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        DashboardPageInterface $dashboardPage,
        HomePageInterface $homePage,
        LoginPageInterface $loginPage,
        RegisterPageInterface $registerPage,
        VerificationPageInterface $verificationPage,
        ProfileUpdatePageInterface $profileUpdatePage,
        RegisterElementInterface $registerElement,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->dashboardPage = $dashboardPage;
        $this->homePage = $homePage;
        $this->loginPage = $loginPage;
        $this->registerPage = $registerPage;
        $this->verificationPage = $verificationPage;
        $this->profileUpdatePage = $profileUpdatePage;
        $this->registerElement = $registerElement;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When /^I want to(?:| again) register a new account$/
     */
    public function iWantToRegisterANewAccount(): void
    {
        $this->registerPage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I do not specify the first name
     */
    public function iSpecifyTheFirstName(?string $firstName = null): void
    {
        $this->registerElement->specifyFirstName($firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I do not specify the last name
     */
    public function iSpecifyTheLastName(?string $lastName = null): void
    {
        $this->registerElement->specifyLastName($lastName);
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail(?string $email = null): void
    {
        $this->registerElement->specifyEmail($email);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs(?string $password = null): void
    {
        $this->registerElement->specifyPassword($password);
        $this->sharedStorage->set('password', $password);
    }

    /**
     * @When /^I confirm (this password)$/
     */
    public function iConfirmThisPassword(string $password): void
    {
        $this->registerElement->verifyPassword($password);
    }

    /**
     * @Given I do not confirm the password
     */
    public function iDoNotConfirmPassword(): void
    {
        $this->registerElement->verifyPassword(null);
    }

    /**
     * @When I specify the phone number as :phoneNumber
     */
    public function iSpecifyThePhoneNumberAs(string $phoneNumber): void
    {
        $this->registerElement->specifyPhoneNumber($phoneNumber);
    }

    /**
     * @When I register this account
     * @When I try to register this account
     */
    public function iRegisterThisAccount(): void
    {
        $this->registerElement->register();
    }

    /**
     * @Then my email should be :email
     * @Then my email should still be :email
     */
    public function myEmailShouldBe(string $email): void
    {
        $this->dashboardPage->open();

        Assert::true($this->dashboardPage->hasCustomerEmail($email));
    }

    /**
     * @Then /^I should be notified that the ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter your %s.', $element));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        $this->assertFieldValidationMessage('email', 'This email is already used.');
    }

    /**
     * @Then I should be notified that the password do not match
     */
    public function iShouldBeNotifiedThatThePasswordDoNotMatch(): void
    {
        $this->assertFieldValidationMessage('password', 'The entered passwords don\'t match');
    }

    /**
     * @Then I should be notified that new account has been successfully created
     * @Then I should be notified that my account has been created and the verification email has been sent
     */
    public function iShouldBeNotifiedThatNewAccountHasBeenSuccessfullyCreated(): void
    {
        $this->notificationChecker->checkNotification(
            'Thank you for registering, check your email to verify your account.',
            NotificationType::success()
        );
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
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
     * @Then I should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->iLogInAsWithPassword($email, $password);
        $this->iShouldBeLoggedIn();
    }

    /**
     * @Then I should not be able to log in as :email with :password password
     */
    public function iShouldNotBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->iLogInAsWithPassword($email, $password);

        Assert::true($this->loginPage->hasValidationErrorWith('Error Account is disabled.'));
    }

    /**
     * @When I log in as :email with :password password
     */
    public function iLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();
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
     * @Then /^my account should be verified$/
     */
    public function myAccountShouldBeVerified(): void
    {
        Assert::true($this->dashboardPage->isVerified());
    }

    /**
     * @When /^(I) try to verify my account using the link from this email$/
     */
    public function iUseItToVerify(ShopUserInterface $user): void
    {
        $this->verificationPage->verifyAccount($user->getEmailVerificationToken());
    }

    /**
     * @When I verify my account using link sent to :customer
     */
    public function iVerifyMyAccount(CustomerInterface $customer): void
    {
        $user = $customer->getUser();
        Assert::notNull($user, 'No account for given customer');

        $this->iUseItToVerify($user);
    }

    /**
     * @When I resend the verification email
     */
    public function iResendVerificationEmail(): void
    {
        $this->dashboardPage->open();
        $this->dashboardPage->pressResendVerificationEmail();
    }

    /**
     * @When I use the verification link from the first email to verify
     */
    public function iUseVerificationLinkFromFirstEmailToVerify(): void
    {
        $token = $this->sharedStorage->get('verification_token');

        $this->verificationPage->verifyAccount($token);
    }

    /**
     * @When I (try to )verify using :token token
     */
    public function iTryToVerifyUsing(string $token): void
    {
        $this->verificationPage->verifyAccount($token);
    }

    /**
     * @Then /^(?:my|his|her) account should not be verified$/
     */
    public function myAccountShouldNotBeVerified(): void
    {
        $this->dashboardPage->open();

        Assert::false($this->dashboardPage->isVerified());
    }

    /**
     * @Then I should not be able to resend the verification email
     */
    public function iShouldBeUnableToResendVerificationEmail(): void
    {
        $this->dashboardPage->open();

        Assert::false($this->dashboardPage->hasResendVerificationEmailButton());
    }

    /**
     * @Then I should be notified that the verification was successful
     */
    public function iShouldBeNotifiedThatTheVerificationWasSuccessful(): void
    {
        $this->notificationChecker->checkNotification('has been successfully verified.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the verification token is invalid
     */
    public function iShouldBeNotifiedThatTheVerificationTokenIsInvalid(): void
    {
        $this->notificationChecker->checkNotification('The verification token is invalid.', NotificationType::failure());
    }

    /**
     * @Then I should be notified that the verification email has been sent
     */
    public function iShouldBeNotifiedThatTheVerificationEmailHasBeenSent(): void
    {
        $this->notificationChecker->checkNotification(
            'An email with the verification link has been sent to your email address.',
            NotificationType::success()
        );
    }

    /**
     * @When I subscribe to the newsletter
     */
    public function iSubscribeToTheNewsletter(): void
    {
        $this->registerElement->subscribeToTheNewsletter();
    }

    /**
     * @Then I should be subscribed to the newsletter
     */
    public function iShouldBeSubscribedToTheNewsletter(): void
    {
        $this->profileUpdatePage->open();

        Assert::true($this->profileUpdatePage->isSubscribedToTheNewsletter());
    }

    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        Assert::true($this->registerElement->checkValidationMessageFor($element, $expectedMessage));
    }
}
