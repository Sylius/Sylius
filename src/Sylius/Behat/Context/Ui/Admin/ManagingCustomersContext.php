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
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\CreatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ManagingCustomersContext implements Context
{
    const RESOURCE_NAME = 'customer';

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

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
     * @Given I want to create a new customer
     */
    public function iWantToCreateANewCustomer()
    {
        $this->createPage->open();
    }

    /**
     * @When /^I specify (?:its|his) first name as "([^"]*)"$/
     */
    public function iSpecifyItsFirstNameAs($name)
    {
        $this->createPage->specifyFirstName($name);
    }

    /**
     * @When /^I specify (?:its|his) last name as "([^"]*)"$/
     */
    public function iSpecifyItsLastNameAs($name)
    {
        $this->createPage->specifyLastName($name);
    }

    /**
     * @When I specify its email as :name
     */
    public function iSpecifyItsEmailAs($email)
    {
        $this->createPage->specifyEmail($email);
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
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfulCreation()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then the customer :customer should appear in the registry
     * @Then the customer :customer should still have this email
     */
    public function thisCustomerShouldAppearInTheRegistry(CustomerInterface $customer)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['Email' => $customer->getEmail()]),
            sprintf('Customer with email %s should exist but it does not.', $customer->getEmail())
        );
    }

    /**
     * @When I select :gender as its gender
     */
    public function iSelectGender($gender)
    {
        $this->createPage->chooseGender($gender);
    }

    /**
     * @When I specify its birthday as :birthday
     */
    public function iSpecifyItsBirthdayAs($birthday)
    {
        $this->createPage->specifyBirthday($birthday);
    }

    /**
     * @Given I want to edit the customer :customer
     */
    public function iWantToEditThisCustomer(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then the customer :customer with name :name should appear in the registry
     */
    public function theCustomerWithNameShouldAppearInTheRegistry(CustomerInterface $customer, $name)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq(
            $name,
            $this->updatePage->getFullName(),
            sprintf('Customer with name %s should exist but it does not.', $name)
        );
    }

    /**
     * @When I want to see all customers in store
     */
    public function iWantToSeeAllCustomersInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) customers in the list$/
     */
    public function iShouldSeeCustomersInTheList($amountOfCustomers)
    {
        Assert::eq(
            (int) $amountOfCustomers,
            $this->indexPage->countItems(),
            sprintf('Amount of customers should be equal %s, but is not.', $amountOfCustomers)
        );
    }

    /**
     * @Then I should see the customer :email in the list
     */
    public function iShouldSeeTheCustomerInTheList($email)
    {
        Assert::true(
            $this->indexPage->isResourceOnPage(['Email' => $email]),
            sprintf('Customer with email %s should exist but it does not.', $email)
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired($elementName)
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor($elementName, sprintf('Please enter your %s.', $elementName)),
            sprintf('Customer % should be required.', $elementName)
        );
    }

    /**
     * @Then the customer with email :email should not appear in the registry
     */
    public function theCustomerShouldNotAppearInTheRegistry($email)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isResourceOnPage(['email' => $email]),
            sprintf('Customer with email %s was created, but it should not.', $email)
        );
    }

    /**
     * @When I remove its first name
     */
    public function iRemoveItsFirstName()
    {
        $this->updatePage->changeFirstName('');
    }

    /**
     * @Then the customer :customer should still have first name :firstName
     */
    public function theCustomerShouldStillHaveFirstName(CustomerInterface $customer, $firstName)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq(
            $firstName,
            $this->updatePage->getFirstName(),
            sprintf('Customer should have first name %s, but it does not.', $firstName)
        );
    }

    /**
     * @When I remove its last name
     */
    public function iRemoveItsLastName()
    {
        $this->updatePage->changeLastName('');
    }

    /**
     * @Then the customer :customer should still have last name :lastName
     */
    public function theCustomerShouldStillHaveLastName(CustomerInterface $customer, $lastName)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq(
            $lastName,
            $this->updatePage->getLastName(),
            sprintf('Customer should have last name %s, but it does not.', $lastName)
        );
    }

    /**
     * @When I remove its email
     */
    public function iRemoveItsEmail()
    {
        $this->updatePage->changeEmail('');
    }

    /**
     * @Then I should be notified that email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('email', 'This email is invalid.'),
            sprintf('Customer should have required form of email.')
        );
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('email', 'This email is already used.'),
            sprintf('Unique email violation message should appear on page, but it does not.')
        );
    }

    /**
     * @Then there should still be only one customer with email :email
     */
    public function thereShouldStillBeOnlyOneCustomerWithEmail($email)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['email' => $email]),
            sprintf('Customer with email %s cannot be founded.', $email)
        );
    }

    /**
     * @Given I want to enable :customer
     * @Given I want to disable :customer
     */
    public function iWantToChangeStatusOf(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt()
    {
        $this->updatePage->disable();
    }

    /**
     * @Then the customer :customer should be enabled
     */
    public function thisCustomerShouldBeEnabled(CustomerInterface $customer)
    {
        $this->indexPage->open();

        Assert::eq(
            'Yes',
            $this->indexPage->getCustomerAccountStatus($customer),
            'Customer account should be enabled, but it does not.'
        );
    }

    /**
     * @Then the customer :customer should be disabled
     */
    public function thisCustomerShouldBeDisabled(CustomerInterface $customer)
    {
        $this->indexPage->open();

        Assert::eq(
            'No',
            $this->indexPage->getCustomerAccountStatus($customer),
            'Customer account should be disabled, but it does not.'
        );
    }
}
