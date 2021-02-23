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
use Webmozart\Assert\Assert;

final class ManagingLocalesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @Given /^I want to (?:create|add) a new locale$/
     */
    public function iWantToAddNewLocale(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I choose :localeCode
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
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Locale could not be created'
        );
    }

    /**
     * @Then the store should be available in the :localeCode language
     */
    public function theStoreShouldBeAvailableInTheLanguage(string $localeCode): void
    {
        $response = $this->client->index();
        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $localeCode),
            sprintf('There is no locale with code "%s"', $localeCode)
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
            'Locale has been created successfully, but it should not'
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
            'Locale has been created successfully, but it should not'
        );
        Assert::same($this->responseChecker->getError($response), 'code: Please choose locale code.');
    }
}
