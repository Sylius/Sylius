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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Webmozart\Assert\Assert;

final class ManagingCustomersContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
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
     */
    public function iWantToEditThisCustomer(CustomerInterface $customer): void
    {
        $this->client->buildUpdateRequest(Resources::CUSTOMERS, (string) $customer->getId());
    }

    /**
     * @When I specify their email as :name
     * @When I do not specify their email
     */
    public function iSpecifyItsEmailAs(?string $email = null): void
    {
        if (null !== $email) {
            $this->client->addRequestData('email', $email);
        }
    }

    /**
     * @When I specify their first name as :name
     */
    public function iSpecifyTheirFirstNameAs(string $name): void
    {
        $this->client->addRequestData('firstName', $name);
    }

    /**
     * @When I specify their last name as :name
     */
    public function iSpecifyTheirLastNameAs(string $name): void
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
     * @When I (try to) add them
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I want to see all customers in store
     */
    public function iWantToSeeAllZonesInStore(): void
    {
        $this->client->index(Resources::CUSTOMERS);
    }

    /**
     * @When I do not specify any information
     */
    public function iDoNotSpecifyAnyInformation(): void
    {
        // Intentionally left empty
    }

    /**
     * @When I do not choose create account option
     */
    public function iDoNotChooseCreateAccountOption()
    {
        // Intentionally left blank.
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
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::CUSTOMERS)), $count);
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
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter your %s.', $element),
        );
    }

    /**
     * @Then I should not see create account option
     */
    public function iShouldNotSeeCreateAccountOption(): void
    {
        // Intentionally left empty
    }

    /**
     * @Then I should still be on the customer creation page
     */
    public function iShouldStillBeOnTheCustomerCreationPage(): void
    {
        // Intentionally left empty
    }

    /**
     * @Then I should be able to specify their password
     * @Then I should not be able to specify their password
     */
    public function iShouldBeAbleToSpecifyTheirPassword(): void
    {
        // Intentionally left empty
    }

    /**
     * @Then I should be able to select create account option
     * @Then I should not be able to select create account option
     */
    public function iShouldBeAbleToSelectCreateAccountOption(): void
    {
        // Intentionally left empty
    }
}
