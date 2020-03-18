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
use Sylius\Component\Core\Model\AdminUserInterface;
use Webmozart\Assert\Assert;

final class ManagingAdministratorsContext implements Context
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
     * @When I browse administrators
     * @When I want to browse administrators
     */
    public function iBrowseAdministrators(): void
    {
        $this->client->index();
    }

    /**
     * @When I want to create a new administrator
     */
    public function iWantToCreateANewAdministrator(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I specify its email as :email
     * @When I do not specify its email
     * @When I change its email to :email
     */
    public function iSpecifyItsEmailAs(string $email = null): void
    {
        if ($email !== null) {
            $this->client->addRequestData('email', $email);
        }
    }

    /**
     * @When I specify its name as :username
     * @When I do not specify its name
     * @When I change its name to :username
     */
    public function iSpecifyItsNameAs(string $username = null): void
    {
        if ($username !== null) {
            $this->client->addRequestData('username', $username);
        }
    }

    /**
     * @When I specify its password as :password
     * @When I do not specify its password
     * @When I change its password to :password
     */
    public function iSpecifyItsPasswordAs(string $password = null): void
    {
        if ($password !== null) {
            $this->client->addRequestData('plainPassword', $password);
        }
    }

    /**
     * @When I specify its locale as :localeCode
     */
    public function iSpecifyItsLocaleAs(string $localeCode): void
    {
        $this->client->addRequestData('localeCode', $localeCode);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->client->addRequestData('enabled', true);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $response = $this->client->update();
        $this->responseChecker->isUpdateSuccessful($response);
    }

    /**
     * @When I delete administrator with email :adminUser
     */
    public function iDeleteAdministratorWithEmail(AdminUserInterface $adminUser): void
    {
        $this->client->delete((string) $adminUser->getId());
    }

    /**
     * @When /^I want to edit (this administrator)$/
     */
    public function iWantToEditThisAdministrator(AdminUserInterface $adminUser): void
    {
        $this->client->buildUpdateRequest((string) $adminUser->getId());
    }

    /**
     * @Then I should see a single administrator in the list
     * @Then there should be :count administrators in the list
     */
    public function iShouldSeeAdministratorsInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the administrator :email should appear in the store
     * @Then I should see the administrator :email in the list
     */
    public function theAdministratorShouldAppearInTheStore(string $email): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'email', $email),
            sprintf('Administrator with email %s does not exist', $email)
        );
    }

    /**
     * @Then there should not be :email administrator anymore
     */
    public function thereShouldNotBeAdministratorAnymore(string $email): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'email', $email),
            sprintf('Administrator with email %s exists, but it should not', $email)
        );
    }

    /**
     * @Then there should still be only one administrator with an email :email
     */
    public function thereShouldStillBeOnlyOneAdministratorWithAnEmail(string $email): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(), 'email', $email),
            1,
            sprintf('There is more than one administrator with email %s', $email)
        );
    }

    /**
     * @Then there should still be only one administrator with name :username
     * @Then this administrator with name :username should appear in the store
     */
    public function thisAdministratorWithNameShouldAppearInTheStore(string $username): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(), 'username', $username),
            1,
            sprintf('There is more than one administrator with username %s', $username)
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Administrator could not be created'
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Administrator could not be edited'
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Administrator could not be deleted'
        );
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'email: This email is already used.'
        );
    }

    /**
     * @Then I should be notified that name must be unique
     */
    public function iShouldBeNotifiedThatNameMustBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'username: This username is already used.'
        );
    }

    /**
     * @Then I should be notified that the :elementName is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(string $elementName): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter your %s.', $elementName)
        );
    }

    /**
     * @Then I should be notified that this email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'email: This email is invalid.'
        );
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted(): void
    {
        Assert::false(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Administrator could be deleted'
        );
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot remove currently logged in user.'
        );
    }
}
