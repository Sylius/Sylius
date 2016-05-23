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
use Sylius\Behat\Page\Shop\Account\DashboardPage;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\ProfileUpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class AccountContext implements Context
{
    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;
    
    /**
     * @var ProfileUpdatePageInterface
     */
    private $profileUpdatePage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param DashboardPageInterface $dashboardPage
     * @param ProfileUpdatePageInterface $profileUpdatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        DashboardPageInterface $dashboardPage,
        ProfileUpdatePageInterface $profileUpdatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->dashboardPage = $dashboardPage;
        $this->profileUpdatePage = $profileUpdatePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to modify my customer profile
     */
    public function iWantToModifyMyCustomerProfile()
    {
        $this->profileUpdatePage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName($firstName = null)
    {
        $this->profileUpdatePage->specifyFirstName($firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName($lastName = null)
    {
        $this->profileUpdatePage->specifyLastName($lastName);
    }

    /**
     * @When I specify the email as :email
     * @When I remove the email
     */
    public function iSpecifyTheEmail($email = null)
    {
        $this->profileUpdatePage->specifyEmail($email);
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
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited()
    {
        $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
    }

    /**
     * @Given my name should be :name
     * @Given my name should still be :name
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
     * @Given my email should be :email
     * @Given my email should still be :email
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
     * @Then /^I should be notified that the email is already used$/
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed()
    {
        $this->assertFieldValidationMessage('email', 'This email is already used.');
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        Assert::true(
            $this->profileUpdatePage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('The %s should be required.', $element)
        );
    }
}
