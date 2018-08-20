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
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

class RegistrationAfterCheckoutContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var RegisterPageInterface */
    private $registerPage;

    /** @var ThankYouPageInterface */
    private $thankYouPage;

    /** @var DashboardPageInterface */
    private $dashboardPage;

    /** @var HomePageInterface */
    private $homePage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        RegisterPageInterface $registerPage,
        ThankYouPageInterface $thankYouPage,
        DashboardPageInterface $dashboardPage,
        HomePageInterface $homePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->registerPage = $registerPage;
        $this->thankYouPage = $thankYouPage;
        $this->dashboardPage = $dashboardPage;
        $this->homePage = $homePage;
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
     * @Then I should be notified that new account has been successfully created
     */
    public function iShouldBeNotifiedThatNewAccountHasBeenSuccessfullyCreated(): void
    {
        $this->notificationChecker->checkNotification(
            'Thank you for registering, check your email to verify your account.',
            NotificationType::success()
        );
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
     * @Then my email should be :email
     */
    public function myEmailShouldBe(string $email): void
    {
        $this->dashboardPage->open();

        Assert::true($this->dashboardPage->hasCustomerEmail($email));
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        Assert::true($this->homePage->hasLogoutButton());
    }
}
