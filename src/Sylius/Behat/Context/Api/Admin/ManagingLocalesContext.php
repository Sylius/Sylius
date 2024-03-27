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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ManagingLocalesContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @Given /^I want to (?:create|add) a new locale$/
     */
    public function iWantToAddNewLocale(): void
    {
        $this->client->buildCreateRequest(Resources::LOCALES);
    }

    /**
     * @When I choose :localeCode
     * @When I set code to :code
     * @When I do not choose a code
     */
    public function iChoose(string $localeCode = ''): void
    {
        $this->client->addRequestData('code', $localeCode);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I remove :localeCode locale
     */
    public function iRemoveLocale(string $localeCode): void
    {
        $this->client->delete(Resources::LOCALES, $localeCode);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Locale could not be created',
        );
    }

    /**
     * @Then the store should be available in the :localeCode language
     */
    public function theStoreShouldBeAvailableInTheLanguage(string $localeCode): void
    {
        $response = $this->client->index(Resources::LOCALES);
        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $localeCode),
            sprintf('There is no locale with code "%s"', $localeCode),
        );
    }

    /**
     * @Then I should not be able to choose :localeCode
     */
    public function iShouldNotBeAbleToChoose(string $localeCode): void
    {
        $this->client->addRequestData('code', $localeCode);
        $response = $this->client->create();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Locale has been created successfully, but it should not',
        );
        Assert::same($this->responseChecker->getError($response), 'code: Locale code must be unique.');
    }

    /**
     * @Then I should be notified that a code is required
     */
    public function iShouldBeNotifiedThatACodeIsRequired(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Locale has been created successfully, but it should not',
        );
        Assert::same($this->responseChecker->getError($response), 'code: Please choose locale code.');
    }

    /**
     * @Then I should be notified that the code is invalid
     */
    public function iShouldBeNotifiedThatTheCodeIsInvalid(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Locale has been created successfully, but it should not',
        );
        Assert::same($this->responseChecker->getError($response), 'code: This value is not a valid locale code.');
    }

    /**
     * @Then I should be informed that locale :localeCode has been deleted
     */
    public function iShouldBeInformedThatLocaleHasBeenDeleted(string $localeCode): void
    {
        Assert::same($this->client->getLastResponse()->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @Then only the :localeCode locale should be present in the system
     */
    public function onlyTheLocaleShouldBePresentInTheSystem(string $localeCode): void
    {
        $response = $this->client->index(Resources::LOCALES);
        Assert::true($this->responseChecker->countCollectionItems($response) === 1);
        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $localeCode),
            sprintf('There is no locale with code "%s"', $localeCode),
        );
    }

    /**
     * @Then I should be informed that locale :localeCode is in use and cannot be deleted
     */
    public function iShouldBeInformedThatLocaleIsInUseAndCannotBeDeleted(string $localeCode): void
    {
        Assert::same($this->client->getLastResponse()->getStatusCode(), Response::HTTP_UNPROCESSABLE_ENTITY);
        Assert::same($this->responseChecker->getError($this->client->getLastResponse()), sprintf('Locale "%s" is used.', $localeCode));
    }

    /**
     * @Then the :localeCode locale should be still present in the system
     */
    public function theLocaleShouldBeStillPresentInTheSystem(string $localeCode): void
    {
        $response = $this->client->index(Resources::LOCALES);
        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $localeCode),
            sprintf('There is no locale with code "%s"', $localeCode),
        );
    }
}
