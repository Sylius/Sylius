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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\CreatePageInterface;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\Admin\Customer\UpdatePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Customer\Model\CustomerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ManagingCustomersContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;
    
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
     * @var ShowPageInterface
     */
    private $showPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param ShowPageInterface $showPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        ShowPageInterface $showPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->showPage = $showPage;
    }

    /**
     * @Given I want to create a new customer
     * @Given I want to create a new customer account
     */
    public function iWantToCreateANewCustomer()
    {
        $this->createPage->open();
    }

    /**
     * @When /^I specify (?:their|his) first name as "([^"]*)"$/
     */
    public function iSpecifyItsFirstNameAs($name)
    {
        $this->createPage->specifyFirstName($name);
    }

    /**
     * @When /^I specify (?:their|his) last name as "([^"]*)"$/
     */
    public function iSpecifyItsLastNameAs($name)
    {
        $this->createPage->specifyLastName($name);
    }

    /**
     * @When I specify their email as :name
     */
    public function iSpecifyItsEmailAs($email)
    {
        $this->createPage->specifyEmail($email);
    }

    /**
     * @When I add them
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then the customer :customer should appear in the store
     * @Then the customer :customer should still have this email
     */
    public function theCustomerShould(CustomerInterface $customer)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['email' => $customer->getEmail()]),
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
     * @Given /^I want to edit (this customer)$/
     * @Given I want to edit the customer :customer
     */
    public function iWantToEditThisCustomer(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @Given I want to change my password
     */
    public function iWantToChangeMyPassword()
    {
        $customer = $this->sharedStorage->get('customer');
        
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
     * @Then /^(this customer) with name "([^"]*)" should appear in the store$/
     */
    public function theCustomerWithNameShouldAppearInTheRegistry(CustomerInterface $customer, $name)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq(
            $name,
            $this->updatePage->getFullName(),
            sprintf('Customer should have name %s, but they have %s.', $name, $this->updatePage->getFullName())
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
            $this->indexPage->isSingleResourceOnPage(['email' => $email]),
            sprintf('Customer with email %s should exist but it does not.', $email)
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired($elementName)
    {
        Assert::same($this->createPage->getValidationMessage($elementName), sprintf('Please enter your %s.', $elementName));
    }

    /**
     * @Then /^I should be notified that ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe($elementName, $validationMessage)
    {
        Assert::same(
            $this->updatePage->getValidationMessage($elementName),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage)
        );
    }

    /**
     * @Then the customer with email :email should not appear in the store
     */
    public function theCustomerShouldNotAppearInTheStore($email)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['email' => $email]),
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
     * @Then /^(this customer) should have an empty first name$/
     * @Then the customer :customer should still have an empty first name
     */
    public function theCustomerShouldStillHaveAnEmptyFirstName(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq(
            '',
            $this->updatePage->getFirstName(),
            'Customer should have an empty first name, but it does not.'
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
     * @Then /^(this customer) should have an empty last name$/
     * @Then the customer :customer should still have an empty last name
     */
    public function theCustomerShouldStillHaveAnEmptyLastName(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq(
            '',
            $this->updatePage->getLastName(),
            'Customer should have an empty last name, but it does not.'
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
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is invalid.');
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique()
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is already used.');
    }

    /**
     * @Then there should still be only one customer with email :email
     */
    public function thereShouldStillBeOnlyOneCustomerWithEmail($email)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['email' => $email]),
            sprintf('Customer with email %s cannot be found.', $email)
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
     * @When I enable their account
     */
    public function iEnableIt()
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable their account
     */
    public function iDisableIt()
    {
        $this->updatePage->disable();
    }

    /**
     * @Then /^(this customer) should be enabled$/
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
     * @Then /^(this customer) should be disabled$/
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

    /**
     * @When I specify its password as :password
     */
    public function iSpecifyItsPasswordAs($password)
    {
        $this->createPage->specifyPassword($password);
    }

    /**
     * @When I change my password to :password
     */
    public function iSpecifyMyPasswordAs($password)
    {
        $this->updatePage->changePassword($password);
    }

    /**
     * @When I choose create account option
     */
    public function iChooseCreateAccountOption()
    {
        $this->createPage->selectCreateAccount();
    }

    /**
     * @Then the customer :customer should have an account created
     * @Then /^(this customer) should have an account created$/
     */
    public function theyShouldHaveAnAccountCreated(CustomerInterface $customer)
    {
        Assert::notNull(
            $customer->getUser()->getPassword(),
            'Customer should have an account, but they do not.'
        );
    }

    /**
     * @When I view details of the customer :customer
     */
    public function iViewDetailsOfTheCustomer(CustomerInterface $customer)
    {
        $this->showPage->open(['id' => $customer->getId()]);
    }

    /**
     * @Then his name should be :name
     */
    public function hisNameShouldBe($name)
    {
        Assert::same(
            $name,
            $this->showPage->getCustomerName(),
            'Customer name should be "%s", but it is not.'
        );
    }

    /**
     * @Given he should be registered since :registrationDate
     */
    public function hisRegistrationDateShouldBe($registrationDate)
    {
        Assert::eq(
            new \DateTime($registrationDate),
            $this->showPage->getRegistrationDate(),
            'Customer registration date should be "%s", but it is not.'
        );
    }

    /**
     * @Given his email should be :email
     */
    public function hisEmailShouldBe($email)
    {
        Assert::same(
            $email,
            $this->showPage->getCustomerEmail(),
            'Customer email should be "%s", but it is not'
        );
    }

    /**
     * @Then his shipping address should be :shippingAddress
     */
    public function hisShippingAddressShouldBe($shippingAddress)
    {
        Assert::same(
            str_replace(',', '', $shippingAddress),
            $this->showPage->getShippingAddress(),
            'Customer shipping address should be "%s", but it is not.'
        );
    }

    /**
     * @Then his billing address should be :billingAddress
     */
    public function hisBillingAddressShouldBe($billingAddress)
    {
        Assert::same(
            str_replace(',', '', $billingAddress),
            $this->showPage->getBillingAddress(),
            'Customer billing address should be "%s", but it is not.'
        );
    }

    /**
     * @Then I should see information about no existing account for this customer
     */
    public function iShouldSeeInformationAboutNoExistingAccountForThisCustomer()
    {
        Assert::true(
            $this->showPage->hasAccount(),
            'There should be information about no account, but there is none.'
        );
    }

    /**
     * @Then I should see that this customer is subscribed to the newsletter
     */
    public function iShouldSeeThatThisCustomerIsSubscribedToTheNewsletter()
    {
        Assert::true(
            $this->showPage->isSubscribedToNewsletter(),
            'There should be information that this customer is subscribed to the newsletter.'
        );
    }

    /**
     * @When I make them subscribed to the newsletter
     */
    public function iMakeThemSubscribedToTheNewsletter()
    {
        $this->updatePage->subscribeToTheNewsletter();
    }

    /**
     * @Then this customer should be subscribed to the newsletter
     */
    public function thisCustomerShouldBeSubscribedToTheNewsletter()
    {
        Assert::true(
            $this->updatePage->isSubscribedToTheNewsletter(),
            'This customer should subscribe to the newsletter.'
        );
    }

    /**
     * @Then the province in the shipping address should be :provinceName
     */
    public function theProvinceInTheShippingAddressShouldBe($provinceName)
    {
        Assert::true(
            $this->showPage->hasShippingProvinceName($provinceName),
            sprintf('Cannot find shipping address with province %s', $provinceName)
        );
    }

    /**
     * @Then the province in the billing address should be :provinceName
     */
    public function theProvinceInTheShippingBillingShouldBe($provinceName)
    {
        Assert::true(
            $this->showPage->hasBillingProvinceName($provinceName),
            sprintf('Cannot find shipping address with province %s', $provinceName)
        );
    }
}
