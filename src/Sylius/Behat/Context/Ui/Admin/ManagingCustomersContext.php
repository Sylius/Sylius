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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\CreatePageInterface;
use Sylius\Behat\Page\Admin\Customer\IndexPageInterface as CustomerIndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\Admin\Customer\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class ManagingCustomersContext implements Context
{
    /**
     * @var CustomerIndexPageInterface
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
     * @param CustomerIndexPageInterface $indexPage
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
    public function iWantToCreateANewCustomer(): void
    {
        $this->createPage->open();
    }

    /**
     * @When /^I specify (?:their|his) first name as "([^"]*)"$/
     */
    public function iSpecifyItsFirstNameAs($name): void
    {
        $this->createPage->specifyFirstName($name);
    }

    /**
     * @When /^I specify (?:their|his) last name as "([^"]*)"$/
     */
    public function iSpecifyItsLastNameAs($name): void
    {
        $this->createPage->specifyLastName($name);
    }

    /**
     * @When I specify their email as :name
     * @When I do not specify their email
     */
    public function iSpecifyItsEmailAs($email = null): void
    {
        $this->createPage->specifyEmail($email);
    }

    /**
     * @When I change their email to :email
     * @When I remove its email
     */
    public function iChangeTheirEmailTo(?string $email = null): void
    {
        $this->updatePage->changeEmail($email);
    }

    /**
     * @When I add them
     * @When I try to add them
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @Then the customer :customer should appear in the store
     * @Then the customer :customer should still have this email
     */
    public function theCustomerShould(CustomerInterface $customer): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $customer->getEmail()]));
    }

    /**
     * @When I select :gender as its gender
     */
    public function iSelectGender($gender): void
    {
        $this->createPage->chooseGender($gender);
    }

    /**
     * @When I select :group as their group
     */
    public function iSelectGroup($group): void
    {
        $this->createPage->chooseGroup($group);
    }

    /**
     * @When I specify its birthday as :birthday
     */
    public function iSpecifyItsBirthdayAs($birthday): void
    {
        $this->createPage->specifyBirthday($birthday);
    }

    /**
     * @When /^I want to edit (this customer)$/
     */
    public function iWantToEditThisCustomer(CustomerInterface $customer): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then /^(this customer) with name "([^"]*)" should appear in the store$/
     */
    public function theCustomerWithNameShouldAppearInTheRegistry(CustomerInterface $customer, $name): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::same($this->updatePage->getFullName(), $name);
    }

    /**
     * @When I want to see all customers in store
     */
    public function iWantToSeeAllCustomersInStore(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) customers in the list$/
     */
    public function iShouldSeeCustomersInTheList($amountOfCustomers): void
    {
        Assert::same($this->indexPage->countItems(), (int) $amountOfCustomers);
    }

    /**
     * @Then I should see the customer :email in the list
     */
    public function iShouldSeeTheCustomerInTheList($email): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired($elementName): void
    {
        Assert::same(
            $this->createPage->getValidationMessage($elementName),
            sprintf('Please enter your %s.', $elementName)
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe($elementName, $validationMessage): void
    {
        Assert::same(
            $this->updatePage->getValidationMessage($elementName),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage)
        );
    }

    /**
     * @Then the customer with email :email should not appear in the store
     */
    public function theCustomerShouldNotAppearInTheStore($email): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @When I remove its first name
     */
    public function iRemoveItsFirstName(): void
    {
        $this->updatePage->changeFirstName('');
    }

    /**
     * @Then /^(this customer) should have an empty first name$/
     * @Then the customer :customer should still have an empty first name
     */
    public function theCustomerShouldStillHaveAnEmptyFirstName(CustomerInterface $customer): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq($this->updatePage->getFirstName(), '');
    }

    /**
     * @When I remove its last name
     */
    public function iRemoveItsLastName(): void
    {
        $this->updatePage->changeLastName('');
    }

    /**
     * @Then /^(this customer) should have an empty last name$/
     * @Then the customer :customer should still have an empty last name
     */
    public function theCustomerShouldStillHaveAnEmptyLastName(CustomerInterface $customer): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq($this->updatePage->getLastName(), '');
    }

    /**
     * @Then I should be notified that email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid(): void
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is invalid.');
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique(): void
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is already used.');
    }

    /**
     * @Then there should still be only one customer with email :email
     */
    public function thereShouldStillBeOnlyOneCustomerWithEmail($email): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Given I want to enable :customer
     * @Given I want to disable :customer
     */
    public function iWantToChangeStatusOf(CustomerInterface $customer): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I enable their account
     */
    public function iEnableIt(): void
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable their account
     */
    public function iDisableIt(): void
    {
        $this->updatePage->disable();
    }

    /**
     * @Then /^(this customer) should be enabled$/
     */
    public function thisCustomerShouldBeEnabled(CustomerInterface $customer): void
    {
        $this->indexPage->open();

        Assert::eq($this->indexPage->getCustomerAccountStatus($customer), 'Enabled');
    }

    /**
     * @Then /^(this customer) should be disabled$/
     */
    public function thisCustomerShouldBeDisabled(CustomerInterface $customer): void
    {
        $this->indexPage->open();

        Assert::eq($this->indexPage->getCustomerAccountStatus($customer), 'Disabled');
    }

    /**
     * @When I specify their password as :password
     */
    public function iSpecifyItsPasswordAs($password): void
    {
        $this->createPage->specifyPassword($password);
    }

    /**
     * @When I choose create account option
     */
    public function iChooseCreateAccountOption(): void
    {
        $this->createPage->selectCreateAccount();
    }

    /**
     * @When I browse orders of a customer :customer
     */
    public function iBrowseOrdersOfACustomer(CustomerInterface $customer): void
    {
        $this->ordersIndexPage->open(['id' => $customer->getId()]);
    }

    /**
     * @Then the customer :customer should have an account created
     * @Then /^(this customer) should have an account created$/
     */
    public function theyShouldHaveAnAccountCreated(CustomerInterface $customer): void
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
    public function iViewDetailsOfTheCustomer(CustomerInterface $customer): void
    {
        $this->showPage->open(['id' => $customer->getId()]);
    }

    /**
     * @Then his name should be :name
     */
    public function hisNameShouldBe($name): void
    {
        Assert::same($this->showPage->getCustomerName(), $name);
    }

    /**
     * @Then he should be registered since :registrationDate
     */
    public function hisRegistrationDateShouldBe($registrationDate): void
    {
        Assert::eq($this->showPage->getRegistrationDate(), new \DateTime($registrationDate));
    }

    /**
     * @Then his email should be :email
     */
    public function hisEmailShouldBe($email): void
    {
        Assert::same($this->showPage->getCustomerEmail(), $email);
    }

    /**
     * @Then his phone number should be :phoneNumber
     */
    public function hisPhoneNumberShouldBe($phoneNumber): void
    {
        Assert::same($this->showPage->getCustomerPhoneNumber(), $phoneNumber);
    }

    /**
     * @Then his default address should be :defaultAddress
     */
    public function hisShippingAddressShouldBe($defaultAddress): void
    {
        Assert::same($this->showPage->getDefaultAddress(), str_replace(',', '', $defaultAddress));
    }

    /**
     * @Then I should see information about no existing account for this customer
     */
    public function iShouldSeeInformationAboutNoExistingAccountForThisCustomer(): void
    {
        Assert::true($this->showPage->hasAccount());
    }

    /**
     * @Then I should see that this customer is subscribed to the newsletter
     */
    public function iShouldSeeThatThisCustomerIsSubscribedToTheNewsletter(): void
    {
        Assert::true($this->showPage->isSubscribedToNewsletter());
    }

    /**
     * @Then I should not see information about email verification
     */
    public function iShouldSeeInformationAboutEmailVerification(): void
    {
        Assert::true($this->showPage->hasEmailVerificationInformation());
    }

    /**
     * @When I make them subscribed to the newsletter
     */
    public function iMakeThemSubscribedToTheNewsletter(): void
    {
        $this->updatePage->subscribeToTheNewsletter();
    }

    /**
     * @When I change the password of user :customer to :newPassword
     */
    public function iChangeThePasswordOfUserTo(CustomerInterface $customer, $newPassword): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);
        $this->updatePage->changePassword($newPassword);
        $this->updatePage->saveChanges();
    }

    /**
     * @Then this customer should be subscribed to the newsletter
     */
    public function thisCustomerShouldBeSubscribedToTheNewsletter(): void
    {
        Assert::true($this->updatePage->isSubscribedToTheNewsletter());
    }

    /**
     * @Then the province in the default address should be :provinceName
     */
    public function theProvinceInTheDefaultAddressShouldBe($provinceName): void
    {
        Assert::true($this->showPage->hasDefaultAddressProvinceName($provinceName));
    }

    /**
     * @Then this customer should have :groupName as their group
     */
    public function thisCustomerShouldHaveAsTheirGroup($groupName): void
    {
        /** @var UpdatePageInterface|ShowPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->updatePage, $this->showPage]);

        Assert::same($currentPage->getGroupName(), $groupName);
    }

    /**
     * @Then I should see that this customer has verified the email
     */
    public function iShouldSeeThatThisCustomerHasVerifiedTheEmail(): void
    {
        Assert::true($this->showPage->hasVerifiedEmail());
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList(): void
    {
        Assert::same($this->ordersIndexPage->countItems(), 1);
    }

    /**
     * @Then I should see the order with number :orderNumber in the list
     */
    public function iShouldSeeASingleOrderFromCustomer($orderNumber): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see the order with number :orderNumber in the list
     */
    public function iShouldNotSeeASingleOrderFromCustomer($orderNumber): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @When I do not specify any information
     */
    public function iDoNotSpecifyAnyInformation(): void
    {
        // Intentionally left blank.
    }

    /**
     * @Then I should not be able to specify their password
     */
    public function iShouldNotBeAbleToSpecifyItPassword(): void
    {
        Assert::true($this->createPage->isUserFormHidden());
    }

    /**
     * @Then I should still be on the customer creation page
     */
    public function iShouldBeOnTheCustomerCreationPage(): void
    {
        $this->createPage->verify();
    }

    /**
     * @Then I should be able to select create account option
     */
    public function iShouldBeAbleToSelectCreateAccountOption(): void
    {
        Assert::false($this->createPage->hasCheckedCreateOption());
    }

    /**
     * @Then I should be able to specify their password
     */
    public function iShouldBeAbleToSpecifyItPassword(): void
    {
        Assert::true($this->createPage->hasPasswordField());
    }

    /**
     * @Then I should not be able to select create account option
     */
    public function iShouldNotBeAbleToSelectCreateAccountOption(): void
    {
        Assert::true($this->createPage->hasCheckedCreateOption());
    }

    /**
     * @When I do not choose create account option
     */
    public function iDoNotChooseCreateAccountOption(): void
    {
        // Intentionally left blank.
    }

    /**
     * @Then I should not see create account option
     */
    public function iShouldNotSeeCreateAccountOption(): void
    {
        Assert::false($this->createPage->hasCreateOption());
    }

    /**
     * @Then /^I should be notified that the password must be at least (\d+) characters long$/
     */
    public function iShouldBeNotifiedThatThePasswordMustBeAtLeastCharactersLong($amountOfCharacters): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('password'),
            sprintf('Password must be at least %d characters long.', $amountOfCharacters)
        );
    }

    /**
     * @Then I should see the customer has not placed any orders yet
     */
    public function iShouldSeeTheCustomerHasNotYetPlacedAnyOrders(): void
    {
        Assert::false($this->showPage->hasCustomerPlacedAnyOrders());
    }

    /**
     * @Then /^I should see that they have placed (\d+) orders? in the "([^"]+)" channel$/
     */
    public function iShouldSeeThatTheyHavePlacedOrdersInTheChannel($ordersCount, $channelName): void
    {
        Assert::same($this->showPage->getOrdersCountInChannel($channelName), (int) $ordersCount);
    }

    /**
     * @Then /^I should see that the overall total value of all their orders in the "([^"]+)" channel is "([^"]+)"$/
     */
    public function iShouldSeeThatTheOverallTotalValueOfAllTheirOrdersInTheChannelIs($channelName, $ordersValue): void
    {
        Assert::same($this->showPage->getOrdersTotalInChannel($channelName), $ordersValue);
    }

    /**
     * @Then /^I should see that the average total value of their order in the "([^"]+)" channel is "([^"]+)"$/
     */
    public function iShouldSeeThatTheAverageTotalValueOfTheirOrderInTheChannelIs($channelName, $ordersValue): void
    {
        Assert::same($this->showPage->getOrdersTotalInChannel($channelName), $ordersValue);
    }
}
