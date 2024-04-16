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
use Sylius\Behat\Client\RequestBuilder;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class ManagingAdministratorsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SharedStorageInterface $sharedStorage,
        private \ArrayAccess $minkParameters,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @Given /^I am editing (my) details$/
     * @When /^I want to edit (this administrator)$/
     */
    public function iWantToEditThisAdministrator(AdminUserInterface $adminUser): void
    {
        $this->client->buildUpdateRequest(Resources::ADMINISTRATORS, (string) $adminUser->getId());
    }

    /**
     * @When I browse administrators
     * @When I want to browse administrators
     * @When I try to browse administrators
     */
    public function iBrowseAdministrators(): void
    {
        $this->client->index(Resources::ADMINISTRATORS);
        $this->sharedStorage->set('last_response', $this->client->getLastResponse());
    }

    /**
     * @When I want to create a new administrator
     */
    public function iWantToCreateANewAdministrator(): void
    {
        $this->client->buildCreateRequest(Resources::ADMINISTRATORS);
    }

    /**
     * @When I specify its email as :email
     * @When I do not specify its email
     * @When I change its email to :email
     */
    public function iSpecifyItsEmailAs(?string $email = null): void
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
    public function iSpecifyItsNameAs(?string $username = null): void
    {
        if ($username !== null) {
            $this->client->addRequestData('username', $username);
        }
    }

    /**
     * @When I specify its :field as too long string
     */
    public function iSpecifyItsFieldAsTooLongString(string $field): void
    {
        $this->client->addRequestData(StringInflector::nameToCamelCase(lcfirst(trim(ucwords($field)))), str_repeat('a', 256));
    }

    /**
     * @When I specify its password as :password
     * @When I do not specify its password
     * @When I change its password to :password
     */
    public function iSpecifyItsPasswordAs(?string $password = null): void
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
     * @When I specify its locale as a wrong code
     */
    public function iSpecifyItsLocaleAsWrongCode(): void
    {
        $this->client->addRequestData('localeCode', 'wr_ONG');
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->client->addRequestData('enabled', true);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I delete administrator with email :adminUser
     */
    public function iDeleteAdministratorWithEmail(AdminUserInterface $adminUser): void
    {
        $this->client->delete(Resources::ADMINISTRATORS, (string) $adminUser->getId());
    }

    /**
     * @When /^I (?:upload|update) the "([^"]+)" image as (my) avatar$/
     */
    public function iUploadTheImageAsMyAvatar(string $avatar, AdminUserInterface $administrator): void
    {
        $builder = RequestBuilder::create(
            sprintf('/api/v2/%s/%s', 'admin', Resources::AVATAR_IMAGES),
            Request::METHOD_POST,
        );
        $builder->withHeader('CONTENT_TYPE', 'multipart/form-data');
        $builder->withHeader('HTTP_ACCEPT', 'application/ld+json');
        $builder->withHeader('HTTP_Authorization', 'Bearer ' . $this->sharedStorage->get('token'));
        $builder->withParameter('owner', $this->iriConverter->getIriFromResource($administrator));
        $builder->withFile('file', new UploadedFile($this->minkParameters['files_path'] . $avatar, basename($avatar)));

        $response = $this->client->request($builder->build());

        $this->sharedStorage->set(StringInflector::nameToCode($avatar), $this->responseChecker->getValue($response, '@id'));
    }

    /**
     * @When I remove the avatar
     */
    public function iRemoveTheAvatarImage(): void
    {
        /** @var AdminUserInterface $administrator */
        $administrator = $this->sharedStorage->get('administrator');
        $avatar = $administrator->getAvatar();
        Assert::notNull($avatar);

        $this->client->delete(Resources::AVATAR_IMAGES, (string) $avatar->getId());
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
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::ADMINISTRATORS), 'email', $email),
            sprintf('Administrator with email %s does not exist', $email),
        );
    }

    /**
     * @Then there should not be :email administrator anymore
     */
    public function thereShouldNotBeAdministratorAnymore(string $email): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::ADMINISTRATORS), 'email', $email),
            sprintf('Administrator with email %s exists, but it should not', $email),
        );
    }

    /**
     * @Then there should still be only one administrator with an email :email
     */
    public function thereShouldStillBeOnlyOneAdministratorWithAnEmail(string $email): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::ADMINISTRATORS), 'email', $email),
            1,
            sprintf('There is more than one administrator with email %s', $email),
        );
    }

    /**
     * @Then there should still be only one administrator with name :username
     * @Then this administrator with name :username should appear in the store
     */
    public function thisAdministratorWithNameShouldAppearInTheStore(string $username): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::ADMINISTRATORS), 'username', $username),
            1,
            sprintf('There is more than one administrator with username %s', $username),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Administrator could not be created',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Administrator could not be deleted',
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
     * @Then I should be notified that name must be unique
     */
    public function iShouldBeNotifiedThatNameMustBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'username: This username is already used.',
        );
    }

    /**
     * @Then I should be notified that the :elementName is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(string $elementName): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter your %s.', $elementName),
        );
    }

    /**
     * @Then I should be notified that this email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'email: This email is invalid.',
        );
    }

    /**
     * @Then I should be notified that this :field is too long
     */
    public function iShouldBeNotifiedThatThisFieldIsTooLong(string $field): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s must not be longer than 255 characters.', ucfirst($field)),
        );
    }

    /**
     * @Then I should be notified that this value is not valid locale
     */
    public function iShouldBeNotifiedThatThisValueIsNotValidLocale(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'localeCode: This value is not a valid locale.',
        );
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted(): void
    {
        Assert::false(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Administrator could be deleted',
        );
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot remove currently logged in user.',
        );
    }

    /**
     * @Then /^I should see the "([^"]*)" image as (my) avatar$/
     */
    public function iShouldSeeTheImageAsMyAvatar(string $avatar, AdminUserInterface $administrator): void
    {
        Assert::true($this->responseChecker->hasValue(
            $this->client->show(Resources::ADMINISTRATORS, (string) $administrator->getId()),
            'avatar',
            $this->sharedStorage->get(StringInflector::nameToCode($avatar)),
        ));
    }

    /**
     * @Then I should not see the :avatar avatar image in the additional information section of my account
     */
    public function iShouldNotSeeTheAvatarImage(string $avatar): void
    {
        /** @var AdminUserInterface $administrator */
        $administrator = $this->sharedStorage->get('administrator');

        Assert::true($this->responseChecker->hasValue(
            $this->client->show(Resources::ADMINISTRATORS, (string) $administrator->getId()),
            'avatar',
            null,
        ));
    }

    /**
     * @Then I should see the :avatar avatar image in the top bar next to my name
     * @Then I should not see the :avatar avatar image in the top bar next to my name
     */
    public function iShouldSeeTheAvatarImageInTheTopBarNextToMyName(string $avatar): void
    {
        // intentionally left blank, as it is ui step
    }
}
