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
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ManagingCustomersContext implements Context
{
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
     * @var IndexPageInterface
     */
    private $ordersIndexPage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param ShowPageInterface $showPage
     * @param IndexPageInterface $ordersIndexPage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        ShowPageInterface $showPage,
        IndexPageInterface $ordersIndexPage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->showPage = $showPage;
        $this->ordersIndexPage = $ordersIndexPage;
        $this->currentPageResolver = $currentPageResolver;
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
     * @When I do not specify their email
     */
    public function iSpecifyItsEmailAs($email = null)
    {
        $this->createPage->specifyEmail($email);
    }

    /**
     * @When I add them
     * @When I try to add them
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

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $customer->getEmail()]));
    }

    /**
     * @When I select :gender as its gender
     */
    public function iSelectGender($gender)
    {
        $this->createPage->chooseGender($gender);
    }

    /**
     * @When I select :group as their group
     */
    public function iSelectGroup($group)
    {
        $this->createPage->chooseGroup($group);
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
     * @Then /^(this customer) with name "([^"]*)" should appear in the store$/
     */
    public function theCustomerWithNameShouldAppearInTheRegistry(CustomerInterface $customer, $name)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::same($this->updatePage->getFullName(), $name);
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
        Assert::same($this->indexPage->countItems(), (int) $amountOfCustomers);
    }

    /**
     * @Then I should see the customer :email in the list
     */
    public function iShouldSeeTheCustomerInTheList($email)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired($elementName)
    {
        Assert::same(
            $this->createPage->getValidationMessage($elementName),
            sprintf('Please enter your %s.', $elementName)
        );
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

        Assert::false($this->indexPage->isSingleResourceOnPage(['email' => $email]));
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

        Assert::eq($this->updatePage->getFirstName(), '');
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

        Assert::eq($this->updatePage->getLastName(), '');
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

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
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

        Assert::eq($this->indexPage->getCustomerAccountStatus($customer), 'Enabled');
    }

    /**
     * @Then /^(this customer) should be disabled$/
     */
    public function thisCustomerShouldBeDisabled(CustomerInterface $customer)
    {
        $this->indexPage->open();

        Assert::eq($this->indexPage->getCustomerAccountStatus($customer), 'Disabled');
    }

    /**
     * @When I specify their password as :password
     */
    public function iSpecifyItsPasswordAs($password)
    {
        $this->createPage->specifyPassword($password);
    }

    /**
     * @When I choose create account option
     */
    public function iChooseCreateAccountOption()
    {
        $this->createPage->selectCreateAccount();
    }

    /**
     * @When I browse orders of a customer :customer
     */
    public function iBrowseOrdersOfACustomer(CustomerInterface $customer)
    {
        $this->ordersIndexPage->open(['id' => $customer->getId()]);
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
     * @When /^I view (their) details$/
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
        Assert::same($this->showPage->getCustomerName(), $name);
    }

    /**
     * @Then he should be registered since :registrationDate
     */
    public function hisRegistrationDateShouldBe($registrationDate)
    {
        Assert::eq($this->showPage->getRegistrationDate(), new \DateTime($registrationDate));
    }

    /**
     * @Then his email should be :email
     */
    public function hisEmailShouldBe($email)
    {
        Assert::same($this->showPage->getCustomerEmail(), $email);
    }

    /**
     * @Then his phone number should be :phoneNumber
     */
    public function hisPhoneNumberShouldBe($phoneNumber)
    {
        Assert::same($this->showPage->getCustomerPhoneNumber(), $phoneNumber);
    }

    /**
     * @Then his default address should be :defaultAddress
     */
    public function hisShippingAddressShouldBe($defaultAddress)
    {
        Assert::same($this->showPage->getDefaultAddress(), str_replace(',', '', $defaultAddress));
    }

    /**
     * @Then I should see information about no existing account for this customer
     */
    public function iShouldSeeInformationAboutNoExistingAccountForThisCustomer()
    {
        Assert::true($this->showPage->hasAccount());
    }

    /**
     * @Then I should see that this customer is subscribed to the newsletter
     */
    public function iShouldSeeThatThisCustomerIsSubscribedToTheNewsletter()
    {
        Assert::true($this->showPage->isSubscribedToNewsletter());
    }

    /**
     * @Then I should not see information about email verification
     */
    public function iShouldSeeInformationAboutEmailVerification()
    {
        Assert::true($this->showPage->hasEmailVerificationInformation());
    }

    /**
     * @When I make them subscribed to the newsletter
     */
    public function iMakeThemSubscribedToTheNewsletter()
    {
        $this->updatePage->subscribeToTheNewsletter();
    }

    /**
     * @When I change the password of user :customer to :newPassword
     */
    public function iChangeThePasswordOfUserTo(CustomerInterface $customer, $newPassword)
    {
        $this->updatePage->open(['id' => $customer->getId()]);
        $this->updatePage->changePassword($newPassword);
        $this->updatePage->saveChanges();
    }

    /**
     * @Then this customer should be subscribed to the newsletter
     */
    public function thisCustomerShouldBeSubscribedToTheNewsletter()
    {
        Assert::true($this->updatePage->isSubscribedToTheNewsletter());
    }

    /**
     * @Then the province in the default address should be :provinceName
     */
    public function theProvinceInTheDefaultAddressShouldBe($provinceName)
    {
        Assert::true($this->showPage->hasDefaultAddressProvinceName($provinceName));
    }

    /**
     * @Then this customer should have :groupName as their group
     */
    public function thisCustomerShouldHaveAsTheirGroup($groupName)
    {
        /** @var UpdatePageInterface|ShowPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->updatePage, $this->showPage]);

        Assert::same($currentPage->getGroupName(), $groupName);
    }

    /**
     * @Then I should see that this customer has verified the email
     */
    public function iShouldSeeThatThisCustomerHasVerifiedTheEmail()
    {
        Assert::true($this->showPage->hasVerifiedEmail());
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList()
    {
        Assert::same($this->ordersIndexPage->countItems(), 1);
    }

    /**
     * @Then I should see the order with number :orderNumber in the list
     */
    public function iShouldSeeASingleOrderFromCustomer($orderNumber)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see the order with number :orderNumber in the list
     */
    public function iShouldNotSeeASingleOrderFromCustomer($orderNumber)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @When I do not specify any information
     */
    public function iDoNotSpecifyAnyInformation()
    {
        // Intentionally left blank.
    }

    /**
     * @Then I should not be able to specify their password
     */
    public function iShouldNotBeAbleToSpecifyItPassword()
    {
        Assert::true($this->createPage->isUserFormHidden());
    }

    /**
     * @Then I should still be on the customer creation page
     */
    public function iShouldBeOnTheCustomerCreationPage()
    {
        $this->createPage->verify();
    }

    /**
     * @Then I should be able to select create account option
     */
    public function iShouldBeAbleToSelectCreateAccountOption()
    {
        Assert::false($this->createPage->hasCheckedCreateOption());
    }

    /**
     * @Then I should be able to specify their password
     */
    public function iShouldBeAbleToSpecifyItPassword()
    {
        Assert::true($this->createPage->hasPasswordField());
    }

    /**
     * @Then I should not be able to select create account option
     */
    public function iShouldNotBeAbleToSelectCreateAccountOption()
    {
        Assert::true($this->createPage->hasCheckedCreateOption());
    }

    /**
     * @When I do not choose create account option
     */
    public function iDoNotChooseCreateAccountOption()
    {
        // Intentionally left blank.
    }

    /**
     * @Then I should not see create account option
     */
    public function iShouldNotSeeCreateAccountOption()
    {
        Assert::false($this->createPage->hasCreateOption());
    }

    /**
     * @Then /^I should be notified that the password must be at least (\d+) characters long$/
     */
    public function iShouldBeNotifiedThatThePasswordMustBeAtLeastCharactersLong($amountOfCharacters)
    {
        Assert::same(
            $this->createPage->getValidationMessage('password'),
            sprintf('Password must be at least %d characters long.', $amountOfCharacters)
        );
    }

    /**
     * @Then I should see the customer has not placed any orders yet
     */
    public function iShouldSeeTheCustomerHasNotYetPlacedAnyOrders()
    {
        Assert::false($this->showPage->hasCustomerPlacedAnyOrders());
    }

    /**
     * @Then /^I should see that they have placed (\d+) orders? in the "([^"]+)" channel$/
     */
    public function iShouldSeeThatTheyHavePlacedOrdersInTheChannel($ordersCount, $channelName)
    {
        Assert::same($this->showPage->getOrdersCountInChannel($channelName), (int) $ordersCount);
    }

    /**
     * @Then /^I should see that the overall total value of all their orders in the "([^"]+)" channel is "([^"]+)"$/
     */
    public function iShouldSeeThatTheOverallTotalValueOfAllTheirOrdersInTheChannelIs($channelName, $ordersValue)
    {
        Assert::same($this->showPage->getOrdersTotalInChannel($channelName), $ordersValue);
    }

    /**
     * @Then /^I should see that the average total value of their order in the "([^"]+)" channel is "([^"]+)"$/
     */
    public function iShouldSeeThatTheAverageTotalValueOfTheirOrderInTheChannelIs($channelName, $ordersValue)
    {
        Assert::same($this->showPage->getOrdersTotalInChannel($channelName), $ordersValue);
    }
}
