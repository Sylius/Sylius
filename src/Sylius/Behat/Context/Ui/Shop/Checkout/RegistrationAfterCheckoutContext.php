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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\Account\VerificationPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class RegistrationAfterCheckoutContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var LoginPageInterface */
    private $loginPage;

    /** @var RegisterPageInterface */
    private $registerPage;

    /** @var ThankYouPageInterface */
    private $thankYouPage;

    /** @var DashboardPageInterface */
    private $dashboardPage;

    /** @var HomePageInterface */
    private $homePage;

    /** @var VerificationPageInterface */
    private $verificationPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        LoginPageInterface $loginPage,
        RegisterPageInterface $registerPage,
        ThankYouPageInterface $thankYouPage,
        DashboardPageInterface $dashboardPage,
        HomePageInterface $homePage,
        VerificationPageInterface $verificationPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->loginPage = $loginPage;
        $this->registerPage = $registerPage;
        $this->thankYouPage = $thankYouPage;
        $this->dashboardPage = $dashboardPage;
        $this->homePage = $homePage;
        $this->verificationPage = $verificationPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I specify a password as :password
     */
    public function iSpecifyThePasswordAs(string $password): void
    {
        $this->registerPage->specifyPassword($password);
        $this->sharedStorage->set('password', $password);
    }

    /**
     * @When /^I confirm (this password)$/
     */
    public function iConfirmThisPassword(string $password): void
    {
        $this->registerPage->verifyPassword($password);
    }

    /**
     * @When I register this account
     */
    public function iRegisterThisAccount(): void
    {
        $this->registerPage->register();
    }

    /**
     * @When I verify my account using link sent to :customer
     */
    public function iVerifyMyAccountUsingLink(CustomerInterface $customer): void
    {
        $user = $customer->getUser();
        Assert::notNull($user, 'No account for given customer');

        $this->verificationPage->verifyAccount($user->getEmailVerificationToken());
    }

    /**
     * @Then the registration form should be prefilled with :email email
     */
    public function theRegistrationFormShouldBePrefilledWithEmail(string $email): void
    {
        $this->thankYouPage->createAccount();

        Assert::same($this->registerPage->getEmail(), $email);
    }

    /**
     * @Then I should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();

        Assert::true($this->homePage->hasLogoutButton());
    }
}
