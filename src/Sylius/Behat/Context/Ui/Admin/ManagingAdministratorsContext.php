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
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Administrator\CreatePageInterface;
use Sylius\Behat\Page\Admin\Administrator\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingAdministratorsContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new administrator
     */
    public function iWantToCreateANewAdministrator()
    {
        $this->createPage->open();
    }

    /**
     * @Given /^I want to edit (this administrator)$/
     * @Given /^I am editing (my) details$/
     */
    public function iWantToEditThisAdministrator(AdminUserInterface $adminUser)
    {
        $this->updatePage->open(['id' => $adminUser->getId()]);
    }

    /**
     * @When I want to browse administrators
     */
    public function iWantToBrowseAdministrators()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its name as :username
     * @When I do not specify its name
     */
    public function iSpecifyItsNameAs($username = null)
    {
        $this->createPage->specifyUsername($username);
    }

    /**
     * @When I change its name as :username
     */
    public function iChangeItsNameAs($username)
    {
        $this->updatePage->changeUsername($username);
    }

    /**
     * @When I specify its email as :email
     * @When I do not specify its email
     */
    public function iSpecifyItsEmailAs($email = null)
    {
        $this->createPage->specifyEmail($email);
    }

    /**
     * @When I change its email as :email
     */
    public function iChangeItsEmailAs($email)
    {
        $this->updatePage->changeEmail($email);
    }

    /**
     * @When I specify its locale to :localeCode
     */
    public function iSpecifyItsLocaleTo($localeCode)
    {
        $this->createPage->specifyLocale($localeCode);
    }

    /**
     * @When I set my locale to :localeCode
     */
    public function iSetMyLocaleTo($localeCode)
    {
        $this->updatePage->changeLocale($localeCode);
        $this->updatePage->saveChanges();
    }

    /**
     * @When I specify its password as :password
     * @When I do not specify its password
     */
    public function iSpecifyItsPasswordAs($password = null)
    {
        $this->createPage->specifyPassword($password);
    }

    /**
     * @When I change its password as :password
     */
    public function iChangeItsPasswordAs($password)
    {
        $this->updatePage->changePassword($password);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->createPage->enable();
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete administrator with email :email
     */
    public function iDeleteAdministratorWithEmail($email)
    {
        $this->indexPage->deleteResourceOnPage(['email' => $email]);
    }

    /**
     * @Then the administrator :email should appear in the store
     * @Then I should see the administrator :email in the list
     * @Then there should still be only one administrator with an email :email
     */
    public function theAdministratorShouldAppearInTheStore($email)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then this administrator with name :username should appear in the store
     * @Then there should still be only one administrator with name :username
     */
    public function thisAdministratorWithNameShouldAppearInTheStore($username)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['username' => $username]));
    }

    /**
     * @Then /^there should be (\d+) administrators in the list$/
     */
    public function iShouldSeeAdministratorsInTheList($number)
    {
        Assert::same($this->indexPage->countItems(), (int) $number);
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique()
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is already used.');
    }

    /**
     * @Then I should be notified that name must be unique
     */
    public function iShouldBeNotifiedThatNameMustBeUnique()
    {
        Assert::same($this->createPage->getValidationMessage('name'), 'This username is already used.');
    }

    /**
     * @Then I should be notified that the :elementName is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired($elementName)
    {
        Assert::same($this->createPage->getValidationMessage($elementName), sprintf('Please enter your %s.', $elementName));
    }

    /**
     * @Then I should be notified that this email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid()
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is invalid.');
    }

    /**
     * @Then this administrator should not be added
     */
    public function thisAdministratorShouldNotBeAdded()
    {
        $this->indexPage->open();

        Assert::same($this->indexPage->countItems(), 1);
    }

    /**
     * @Then there should not be :email administrator anymore
     */
    public function thereShouldBeNoAnymore($email)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted()
    {
        $this->notificationChecker->checkNotification(
            'Cannot remove currently logged in user.',
            NotificationType::failure()
        );
    }
}
