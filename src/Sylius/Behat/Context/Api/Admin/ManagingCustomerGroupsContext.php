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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Webmozart\Assert\Assert;

final class ManagingCustomerGroupsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(ApiClientInterface $client, ResponseCheckerInterface $responseChecker)
    {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I want to create a new customer group
     */
    public function iWantToCreateANewCustomerGroup(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        if ($code !== null) {
            $this->client->addRequestData('code', $code);
        }
    }

    /**
     * @When I specify its name as :name
     */
    public function iSpecifyItsNameAs(string $name): void
    {
        $this->client->addRequestData('name', $name);
    }

    /**
     * @When I remove its name
     */
    public function iRemoveItsName(): void
    {
        $this->client->addRequestData('name', '');
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->client->create();
    }

    /**
     * @When /^I want to edit (this customer group)$/
     */
    public function iWantToEditThisCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->client->buildUpdateRequest($customerGroup->getCode());
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I browse customer groups
     * @When I want to browse customer groups
     */
    public function iWantToBrowseCustomerGroups(): void
    {
        $this->client->index();
    }

    /**
     * @When I delete the :customerGroup customer group
     */
    public function iDeleteTheCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->client->delete($customerGroup->getCode());
    }

    /**
     * @Then the customer group :customerGroup should appear in the store
     */
    public function theCustomerGroupShouldAppearInTheStore(CustomerGroupInterface $customerGroup): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'code', $customerGroup->getCode()),
            sprintf('Customer group with code %s does not exist', $customerGroup->getCode())
        );
    }

    /**
     * @Then this customer group with name :name should appear in the store
     * @Then I should see the customer group :name in the list
     */
    public function thisCustomerGroupWithNameShouldAppearInTheStore(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('Customer group with name %s does not exist', $name)
        );
    }

    /**
     * @Then I should see a single customer group in the list
     * @Then I should see :amountOfCustomerGroups customer groups in the list
     */
    public function iShouldSeeCustomerGroupsInTheList(int $amountOfCustomerGroups = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index()), $amountOfCustomerGroups);
    }

    /**
     * @Then /^(this customer group) should still be named "([^"]+)"$/
     */
    public function thisCustomerGroupShouldStillBeNamed(CustomerGroupInterface $customerGroup, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show($customerGroup->getCode()), 'name', $name),
            'Customer groups name is not ' . $name
        );
    }

    /**
     * @Then I should be notified that name is required
     */
    public function iShouldBeNotifiedThatNameIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'name: Please enter a customer group name.'
        );
    }

    /**
     * @Then I should be notified that customer group with this code already exists
     */
    public function iShouldBeNotifiedThatCustomerGroupWithThisCodeAlreadyExists(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Customer group code has to be unique.'
        );
    }

    /**
     * @Then I should be informed that this form contains errors
     */
    public function iShouldBeInformedThatThisFormContainsErrors(): void
    {
        Assert::notEmpty($this->responseChecker->getError($this->client->getLastResponse()));
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The code field with value NEW_CODE exist'
        );
    }

    /**
     * @Then /^(this customer group) should no longer exist in the registry$/
     */
    public function thisCustomerGroupShouldNoLongerExistInTheRegistry(CustomerGroupInterface $customerGroup)
    {
        $code = $customerGroup->getCode();
        Assert::false($this->isItemOnIndex('code', $code), sprintf('Customer group with code %s exist', $code));
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Customer group could not be created'
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Customer group could not be edited'
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful(
            $this->client->getLastResponse()),
            'Customer group could not be deleted'
        );
    }

    private function isItemOnIndex(string $property, string $value): bool
    {
        return $this->responseChecker->hasItemWithValue($this->client->index(), $property, $value);
    }
}
