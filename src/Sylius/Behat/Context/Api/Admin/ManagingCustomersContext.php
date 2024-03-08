<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Webmozart\Assert\Assert;

final class ManagingCustomersContext implements Context
{
    public const SORT_TYPES = ['ascending' => 'asc', 'descending' => 'desc'];

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to create a new customer
     * @When I want to create a new customer account
     */
    public function iWantToCreateANewCustomer(): void
    {
        $this->client->buildCreateRequest(Resources::CUSTOMERS);
    }

    /**
     * @When /^I want to edit (this customer)$/
     * @When I want to enable :customer
     * @When I want to disable :customer
     * @When I want to verify :customer
     */
    public function iWantToEditThisCustomer(CustomerInterface $customer): void
    {
        $this->client->buildUpdateRequest(Resources::CUSTOMERS, (string) $customer->getId());
    }

    /**
     * @When I browse orders of a customer :customer
     */
    public function iBrowseOrdersOfACustomer(CustomerInterface $customer): void
    {
        $this->client->index(Resources::ORDERS);
        $this->client->addFilter('customer.id', $customer->getId());
        $this->client->filter();
    }

    /**
     * @When I specify their email as :email
     * @When I do not specify their email
     * @When I change their email to :email
     * @When I remove its email
     */
    public function iChangeTheirEmailTo(?string $email = null): void
    {
        $this->client->addRequestData('email', (string) $email);
    }

    /**
     * @When /^I specify (?:their|his) first name as "([^"]*)"$/
     * @When I remove its first name
     */
    public function iSpecifyTheirFirstNameAs(?string $name = null): void
    {
        $this->client->addRequestData('firstName', $name);
    }

    /**
     * @When /^I specify (?:their|his) last name as "([^"]*)"$/
     * @When I remove its last name
     */
    public function iSpecifyTheirLastNameAs(?string $name = null): void
    {
        $this->client->addRequestData('lastName', $name);
    }

    /**
     * @When I specify its birthday as :birthday
     */
    public function iSpecifyItsBirthdayAs(string $birthday): void
    {
        $this->client->addRequestData('birthday', $birthday);
    }

    /**
     * @When I select :gender as its gender
     */
    public function iSelectGender(string $gender): void
    {
        $this->client->addRequestData('gender', strtolower(substr($gender, 0, 1)));
    }

    /**
     * @When I select :customerGroup as their group
     */
    public function iSelectGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->client->addRequestData('group', $this->iriConverter->getIriFromItem($customerGroup));
    }

    /**
     * @When I make them subscribed to the newsletter
     */
    public function iMakeThemSubscribedToTheNewsletter(): void
    {
        $this->client->addRequestData('subscribedToNewsletter', true);
    }

    /**
     * @When I choose create account option
     */
    public function iChooseCreateAccountOption(): void
    {
        $this->client->addRequestData('user', []);
    }

    /**
     * @When I specify their password as :password
     */
    public function iSpecifyItsPasswordAs(string $password): void
    {
        $this->client->addRequestData('user', [
            'plainPassword' => $password,
        ]);
    }

    /**
     * @When /^I (enable|disable) their account$/
     */
    public function iEnableIt(string $toggleAction): void
    {
        $this->client->addRequestData('user', [
            'enabled' => 'enable' === $toggleAction,
        ]);
    }

    /**
     * @When I verify it
     */
    public function iVerifyIt(): void
    {
        $this->client->addRequestData('user', [
            'verified' => true,
        ]);
    }

    /**
     * @When I (try to) add them
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I want to see all customers in store
     */
    public function iWantToSeeAllCustomersInStore(): void
    {
        $this->client->index(Resources::CUSTOMERS);
    }

    /**
     * @When I view details of the customer :customer
     * @When /^I view (their) details$/
     */
    public function iViewDetailsOfTheCustomer(CustomerInterface $customer): void
    {
        $this->client->show(Resources::CUSTOMERS, (string) $customer->getId());
    }

    /**
     * @When I filter by group :groupName
     * @When I filter by groups :firstGroup and :secondGroup
     */
    public function iFilterByGroup(string ...$groupsNames): void
    {
        foreach ($groupsNames as $groupName) {
            $this->client->addFilter('group.name[]', $groupName);
        }
        $this->client->filter();
    }

    /**
     * @When I sort the orders :sortType by channel
     */
    public function iSortThemBy(string $sortType = 'ascending'): void
    {
        $this->client->sort([
            'channel.code' => self::SORT_TYPES[$sortType],
        ]);
    }

    /**
     * @When I change the password of user :customer to :newPassword
     */
    public function iChangeThePasswordOfUserTo(CustomerInterface $customer, string $newPassword): void
    {
        $this->iWantToEditThisCustomer($customer);
        $this->iSpecifyItsPasswordAs($newPassword);
        $this->client->update();
    }

    /**
     * @When I delete the account of :shopUser user
     */
    public function iDeleteAccount(ShopUserInterface $shopUser): void
    {
        $this->sharedStorage->set('customer', $shopUser->getCustomer());
        $this->client->delete(sprintf('customers/%s', $shopUser->getCustomer()->getId()), 'user');
    }

    /**
     * @When I do not specify any information
     * @When I do not choose create account option
     */
    public function intentionallyLeftEmpty(): void
    {
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Customer could not be created',
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter your %s.', $element),
        );
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'email: This email is already used.',
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe(string $elementName, string $validationMessage): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage),
        );
    }

    /**
     * @Then I should be notified that email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'This email is invalid.',
        );
    }

    /**
     * @Then the customer :customer should appear in the store
     * @Then the customer :customer should still have this email
     */
    public function theCustomerShouldAppearInTheStore(CustomerInterface $customer): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::CUSTOMERS), 'email', $customer->getEmail()),
            sprintf('Customer with email %s does not exist', $customer->getEmail()),
        );
    }

    /**
     * @Then the customer :customer should have an account created
     * @Then /^(this customer) should have an account created$/
     */
    public function theyShouldHaveAnAccountCreated(CustomerInterface $customer): void
    {
        Assert::notNull(
            $customer->getUser()->getPassword(),
            'Customer should have an account, but they do not.',
        );
    }

    /**
     * @Then I should see :count customers in the list
     * @Then I should see a single customer on the list
     */
    public function iShouldSeeZonesInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see the customer :email in the list
     */
    public function iShouldSeeTheCustomerInTheList(string $email): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::CUSTOMERS), 'email', $email),
            sprintf('There is no customer with email "%s"', $email),
        );
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList(): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), 1);
    }

    /**
     * @Then their name should be :name
     */
    public function theirNameShouldBe(string $name): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'fullName', $name));
    }

    /**
     * @Then he should be registered since :registrationDate
     */
    public function hisRegistrationDateShouldBe(string $registrationDate): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'createdAt', $registrationDate));
    }

    /**
     * @Then their email should be :email
     */
    public function theirEmailShouldBe(string $email): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'email', $email));
    }

    /**
     * @Then their phone number should be :phoneNumber
     */
    public function theirPhoneNumberShouldBe(string $phoneNumber): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'phoneNumber', $phoneNumber));
    }

    /**
     * @Then their default address should be :firstName :lastName, :street, :postcode :city, :country
     */
    public function theirSDefaultAddressShouldBe(
        string $firstName,
        string $lastName,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country,
    ): void {
        $this->client->showByIri($this->responseChecker->getValue($this->client->getLastResponse(), 'defaultAddress'));

        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'firstName'), $firstName);
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'lastName'), $lastName);
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'street'), $street);
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'postcode'), $postcode);
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'city'), $city);
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'countryCode'), $country->getCode());
    }

    /**
     * @Then the province in the default address should be :provinceName
     */
    public function theProvinceInTheDefaultAddressShouldBe(string $provinceName): void
    {
        $this->client->showByIri($this->responseChecker->getValue($this->client->getLastResponse(), 'defaultAddress'));
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'provinceName'), $provinceName);
    }

    /**
     * @Then I should see information about no existing account for this customer
     * @Then I should not see information about email verification
     */
    public function iShouldSeeInformationAboutNoExistingAccountForThisCustomer(): void
    {
        Assert::null($this->responseChecker->getValue($this->client->getLastResponse(), 'user'));
    }

    /**
     * @Then I should see that this customer has verified the email
     */
    public function iShouldSeeThatThisCustomerHasVerifiedTheEmail(): void
    {
        $user = $this->responseChecker->getValue($this->client->getLastResponse(), 'user');
        Assert::true($user['verified']);
    }

    /**
     * @Then I should see the order with number :orderNumber in the list
     */
    public function iShouldSeeTheOrderWithNumberInTheList(string $orderNumber): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'number',
                $orderNumber,
            ),
        );
    }

    /**
     * @Then I should be notified that the password must be at least :amountOfCharacters characters long
     */
    public function iShouldBeNotifiedThatThePasswordMustBeAtLeastCharactersLong(int $amountOfCharacters): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Password must be at least %d characters long.', $amountOfCharacters),
        );
    }

    /**
     * @Then I should not see the order with number :orderNumber in the list
     */
    public function iShouldNotSeeASingleOrderFromCustomer(string $orderNumber): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'number',
                $orderNumber,
            ),
        );
    }

    /**
     * @Then /^(this customer) should be (enabled|disabled)$/
     */
    public function thisCustomerShouldBeEnabled(CustomerInterface $customer, string $toggleAction): void
    {
        $user = $this->responseChecker->getValue(
            $this->client->show(Resources::CUSTOMERS, (string) $customer->getId()),
            'user',
        );
        Assert::same($user['enabled'], 'enabled' === $toggleAction);
    }

    /**
     * @Then /^(this customer) should be verified$/
     */
    public function thisCustomerShouldBeVerified(CustomerInterface $customer): void
    {
        $user = $this->responseChecker->getValue(
            $this->client->show(Resources::CUSTOMERS, (string) $customer->getId()),
            'user',
        );
        Assert::true($user['verified']);
    }

    /**
     * @Then there should still be only one customer with email :email
     */
    public function thereShouldStillBeOnlyOneCustomerWithEmail(string $email): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::CUSTOMERS), 'email', $email),
            1,
            sprintf('There is more than one customer with email %s', $email),
        );
    }

    /**
     * @Then /^(this customer) should have an empty first name$/
     * @Then the customer :customer should still have an empty first name
     */
    public function theCustomerShouldStillHaveAnEmptyFirstName(CustomerInterface $customer): void
    {
        Assert::null(
            $this->responseChecker->getValue(
                $this->client->show(Resources::CUSTOMERS, (string) $customer->getId()),
                'firstName',
            ),
        );
    }

    /**
     * @Then /^(this customer) should have an empty last name$/
     * @Then the customer :customer should still have an empty last name
     */
    public function theCustomerShouldStillHaveAnEmptyLastName(CustomerInterface $customer): void
    {
        Assert::null(
            $this->responseChecker->getValue(
                $this->client->show(Resources::CUSTOMERS, (string) $customer->getId()),
                'lastName',
            ),
        );
    }

    /**
     * @Then the customer with email :email should not appear in the store
     */
    public function theCustomerShouldNotAppearInTheStore(string $email): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue(
                $this->client->index(Resources::CUSTOMERS),
                'email',
                $email,
            ),
        );
    }

    /**
     * @Then /^(this customer) with name "([^"]*)" should appear in the store$/
     */
    public function theCustomerWithNameShouldAppearInTheStore(CustomerInterface $customer, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::CUSTOMERS, (string) $customer->getId()),
                'fullName',
                $name,
            ),
        );
    }

    /**
     * @Then this customer should be subscribed to the newsletter
     * @Then I should see that this customer is subscribed to the newsletter
     */
    public function thisCustomerShouldBeSubscribedToTheNewsletter(): void
    {
        Assert::true(
            $this->responseChecker->getValue(
                $this->client->getLastResponse(),
                'subscribedToNewsletter',
            ),
        );
    }

    /**
     * @Then this customer should have :customerGroup as their group
     */
    public function thisCustomerShouldHaveAsTheirGroup(CustomerGroupInterface $customerGroup): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'group'),
            $this->iriConverter->getIriFromItem($customerGroup),
        );
    }

    /**
     * @Then the customer with this email should still exist
     */
    public function customerShouldStillExist(): void
    {
        /** @var CustomerInterface $customer */
        $customer = $this->sharedStorage->get('customer');

        $this->client->show(Resources::CUSTOMERS, (string) $customer->getId());

        Assert::same($this->client->getLastResponse()->getStatusCode(), 200);
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'email'), $customer->getEmail());
    }

    /**
     * @Then I should not see create account option
     * @Then I should still be on the customer creation page
     * @Then I should be able to specify their password
     * @Then I should not be able to specify their password
     * @Then I should be able to select create account option
     * @Then I should not be able to select create account option
     */
    public function intentionallyLeftBlank(): void
    {
    }

    /**
     * @Then the user account should be deleted
     */
    public function accountShouldBeDeleted(): void
    {
        /** @var CustomerInterface $customer */
        $customer = $this->sharedStorage->get('customer');

        $response = $this->client->show(Resources::CUSTOMERS, (string) $customer->getId());

        Assert::null($this->responseChecker->getValue($response, 'user'));
    }

    /**
     * @Then I should not be able to delete it again
     */
    public function iShouldNotBeAbleToDeleteCustomerAgain(): void
    {
        $customer = $this->sharedStorage->get('customer');
        $this->client->delete(sprintf('customer/%s', $customer->getId()), 'user');

        Assert::same($this->client->getLastResponse()->getStatusCode(), 404);
    }
}
