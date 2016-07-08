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
use Sylius\Behat\Page\Shop\Account\VerificationPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RegistrationContext implements Context
{
    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @var RegisterPageInterface
     */
    private $registerPage;

    /**
     * @var VerificationPageInterface
     */
    private $verificationPage;

    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var EmailCheckerInterface
     */
    private $emailChecker;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param DashboardPageInterface $dashboardPage
     * @param HomePageInterface $homePage
     * @param NotificationCheckerInterface $notificationChecker
     * @param RegisterPageInterface $registerPage
     * @param VerificationPageInterface $verificationPage
     * @param SecurityServiceInterface $securityService
     * @param EmailCheckerInterface $emailChecker
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        CurrentPageResolverInterface $currentPageResolver,
        DashboardPageInterface $dashboardPage,
        HomePageInterface $homePage,
        NotificationCheckerInterface $notificationChecker,
        RegisterPageInterface $registerPage,
        VerificationPageInterface $verificationPage,
        SecurityServiceInterface $securityService,
        EmailCheckerInterface $emailChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->currentPageResolver = $currentPageResolver;
        $this->dashboardPage = $dashboardPage;
        $this->homePage = $homePage;
        $this->notificationChecker = $notificationChecker;
        $this->registerPage = $registerPage;
        $this->verificationPage = $verificationPage;
        $this->securityService = $securityService;
        $this->emailChecker = $emailChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^I want to(?:| again) register a new account$/
     */
    public function iWantToRegisterANewAccount()
    {
        $this->registerPage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I do not specify the first name
     */
    public function iSpecifyTheFirstName($firstName = null)
    {
        $this->registerPage->specifyFirstName($firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I do not specify the last name
     */
    public function iSpecifyTheLastName($lastName = null)
    {
        $this->registerPage->specifyLastName($lastName);
    }

    /**
     * @When I specify the email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $this->registerPage->specifyEmail($email);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs($password = null)
    {
        $this->registerPage->specifyPassword($password);
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
     * @Given I do not confirm the password
     */
    public function iDoNotConfirmPassword()
    {
        $this->registerPage->verifyPassword(null);
    }

    /**
     * @When I specify the phone number as :phoneNumber
     */
    public function iSpecifyThePhoneNumberAs($phoneNumber)
    {
        $this->registerPage->specifyPhoneNumber($phoneNumber);
    }

    /**
     * @When I register this account
     * @When I try to register this account
     */
    public function iRegisterThisAccount()
    {
        $this->registerPage->register();
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
     * @Then /^I should be notified that the ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter your %s.', $element));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed()
    {
        $this->assertFieldValidationMessage('email', 'This email is already used.');
    }

    /**
     * @Then I should be notified that the password do not match
     */
    public function iShouldBeNotifiedThatThePasswordDoNotMatch()
    {
        $this->assertFieldValidationMessage('password', 'The entered passwords don\'t match');
    }

    /**
     * @Then I should be notified that new account has been successfully created
     * @Then I should be notified that my account has been created and the verification email has been sent
     */
    public function iShouldBeNotifiedThatNewAccountHasBeenSuccessfullyCreated()
    {
        $this->notificationChecker->checkNotification(
            'Thank you for registering, check your email to verify your account.',
            NotificationType::success()
        );
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        Assert::true(
            $this->homePage->hasLogoutButton(),
            'I should be on home page and, also i should be able to sign out.'
        );
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        Assert::false(
            $this->homePage->hasLogoutButton(),
            'I should not be logged in.'
        );
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
     * @Then /^(my) account should be verified$/
     */
    public function myAccountShouldBeVerified(UserInterface $user)
    {
        $this->securityService->logIn($user->getEmail());

        Assert::true(
            $this->dashboardPage->isVerified(),
            'My account should be verified.'
        );
    }

    /**
     * @When /^(I) try to verify my account using the link from this email$/
     */
    public function iUseItToVerify(UserInterface $user)
    {
        $this->verificationPage->verifyAccount($user->getEmailVerificationToken());
    }

    /**
     * @When I resend the verification email
     */
    public function iResendVerificationEmail()
    {
        $this->dashboardPage->open();
        $this->dashboardPage->pressResendVerificationEmail();
    }

    /**
     * @When I use the verification link from the first email to verify
     */
    public function iUseVerificationLinkFromFirstEmailToVerify()
    {
        $token = $this->sharedStorage->get('verification_token');

        $this->verificationPage->verifyAccount($token);
    }

    /**
     * @When I (try to )verify using :token token
     */
    public function iTryToVerifyUsing($token)
    {
        $this->verificationPage->verifyAccount($token);
    }

    /**
     * @Then /^(?:my|his|her) account should not be verified$/
     */
    public function myAccountShouldNotBeVerified()
    {
        $this->dashboardPage->open();

        Assert::false(
            $this->dashboardPage->isVerified(),
            'Account should not be verified.'
        );
    }

    /**
     * @Then I should be unable to resend the verification email
     */
    public function iShouldBeUnableToResendVerificationEmail()
    {
        $this->dashboardPage->open();

        Assert::false(
            $this->dashboardPage->hasResendVerificationEmailButton(),
            'You should not be able to resend the verification email.'
        );
    }

    /**
     * @Then I should be notified that the verification was successful
     */
    public function iShouldBeNotifiedThatTheVerificationWasSuccessful()
    {
        $this->notificationChecker->checkNotification('has been successfully verified.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the verification token is invalid
     */
    public function iShouldBeNotifiedThatTheVerificationTokenIsInvalid()
    {
        $this->notificationChecker->checkNotification('The verification token is invalid.', NotificationType::failure());
    }

    /**
     * @Then I should be notified that the verification email has been sent
     */
    public function iShouldBeNotifiedThatTheVerificationEmailHasBeenSent()
    {
        $this->notificationChecker->checkNotification(
            'An email with the verification link has been sent to your email address.',
            NotificationType::success()
        );
    }

    /**
     * @Then the (verification) email should be sent to :email
     */
    public function verificationEmailShouldBeSentTo($email)
    {
        Assert::true(
            $this->emailChecker->hasRecipient($email),
            'The verification email should have been sent.'
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        Assert::true(
            $this->registerPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('The %s should be required.', $element)
        );
    }
}
