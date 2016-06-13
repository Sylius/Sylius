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
use Sylius\Behat\Service\Accessor\EmailCheckerInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
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
        EmailCheckerInterface $emailChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->currentPageResolver = $currentPageResolver;
        $this->dashboardPage = $dashboardPage;
        $this->homePage = $homePage;
        $this->notificationChecker = $notificationChecker;
        $this->registerPage = $registerPage;
        $this->verificationPage = $verificationPage;
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
     * @Then I should be logged in as :email
     */
    public function iShouldBeLoggedInAs($email)
    {
        $this->iShouldBeLoggedIn();
        $this->myEmailShouldBe($email);
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
        $this->registerPage->specifyPhoneNumber(424242420);
        $this->registerPage->register();
    }

    /**
     * @Then my account should be verified
     */
    public function myAccountShouldBeVerified()
    {
        Assert::true(
            $this->dashboardPage->isVerified(),
            'You should be verified.'
        );
    }

    /**
     * @When I use it to verify
     */
    public function iUseItToVerify()
    {
        $user = $this->sharedStorage->get('user');

        $this->verificationPage->verifyAccount($user->getEmail(), $user->getEmailVerificationToken());
    }

    /**
     * @When I resend the verification email
     */
    public function iResendVerificationEmail()
    {
        $this->dashboardPage->open();
        $this->dashboardPage->pressVerify();
    }

    /**
     * @When I use the first email to verify
     */
    public function iVerifyWithFirstEmail()
    {
        $user = $this->sharedStorage->get('user');
        $token = $this->sharedStorage->get('verification_token');

        $this->verificationPage->verifyAccount($user->getEmail(), $token);
    }

    /**
     * @When I try to verify using email :email and token :token
     */
    public function iTryToVerifyUsing($email, $token)
    {
        $this->verificationPage->verifyAccount($email, $token);
    }

    /**
     * @Then my account should not be verified (yet)
     */
    public function myAccountShouldNotBeVerified()
    {
        $this->dashboardPage->open();

        Assert::false(
            $this->dashboardPage->isVerified(),
            'You should not be verified.'
        );
    }

    /**
     * @Then I should be unable to resend the verification email
     */
    public function iShouldBeUnableToResendVerificationEmail()
    {
        $this->dashboardPage->open();

        Assert::false(
            $this->dashboardPage->hasVerificationButton(),
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
     * @Then I should be notified that the verification was not successful
     */
    public function iShouldBeNotifiedThatTheVerificationWasNotSuccessful()
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
     * @Then the verification email should be sent to :email
     * @Then an email should be sent to :email
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
