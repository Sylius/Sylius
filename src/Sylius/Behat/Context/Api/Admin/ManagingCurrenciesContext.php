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

final class ManagingCurrenciesContext implements Context
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
     * @When I want to browse currencies of the store
     */
    public function iWantToSeeAllCurrenciesInStore(): void
    {
        $this->client->index();
    }

    /**
     * @When I want to add a new currency
     */
    public function iWantToAddNewCurrency(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I choose :currencyCode
     */
    public function iChoose(string $currencyCode): void
    {
        $this->client->addRequestData('code', $currencyCode);
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
     * @Then I should see :count currencies in the list
     */
    public function iShouldSeeCurrenciesInTheList(int $count): void
    {
        $itemsCount = $this->responseChecker->countCollectionItems($this->client->getLastResponse());

        Assert::eq($count, $itemsCount, sprintf('Expected %d currencies, but got %d', $count, $itemsCount));
    }

    /**
     * @Then I should see the currency :currencyName in the list
     * @Then the currency :currencyName should appear in the store
     */
    public function currencyShouldAppearInTheStore(string $currencyName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $currencyName),
            sprintf('There is no currency with name "%s"', $currencyName)
        );
    }

    /**
     * @Then there should still be only one currency with code :code
     */
    public function thereShouldStillBeOnlyOneCurrencyWithCode(string $code): void
    {
        $response = $this->client->index();
        Assert::same($this->responseChecker->countCollectionItems($response), 1);
        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $code),
            sprintf('There is no currency with code "%s"', $code)
        );
    }

    /**
     * @Then I should be notified that currency code must be unique
     */
    public function iShouldBeNotifiedThatCurrencyCodeMustBeUnique(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Currency has been created successfully, but it should not'
        );
        Assert::same($this->responseChecker->getError($response), 'code: Currency code must be unique.');
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Currency could not be created'
        );
    }
}
