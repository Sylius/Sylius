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
use Webmozart\Assert\Assert;

final class ManagingCurrenciesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
        $this->client->setResource('currencies');
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
        $itemsCount = $this->client->countCollectionItems();

        Assert::eq($count, $itemsCount, sprintf('Expected %d currencies, but got %d', $count, $itemsCount));
    }

    /**
     * @Then I should see the currency :currencyName in the list
     * @Then the currency :currencyName should appear in the store
     */
    public function currencyShouldAppearInTheStore(string $currencyName): void
    {
        $this->client->index();
        Assert::true(
            $this->client->hasItemWithValue('name', $currencyName),
            sprintf('There is no currency with name "%s"', $currencyName)
        );
    }

    /**
     * @Then there should still be only one currency with code :code
     */
    public function thereShouldStillBeOnlyOneCurrencyWithCode(string $code): void
    {
        $this->client->index();
        Assert::eq(1, $this->client->countCollectionItems());
        Assert::true($this->client->hasItemWithValue('code', $code), sprintf('There is no currency with code "%s"', $code));
    }

    /**
     * @Then I should be notified that currency code must be unique
     */
    public function iShouldBeNotifiedThatCurrencyCodeMustBeUnique(): void
    {
        Assert::false($this->client->isCreationSuccessful(), 'Currency has been created successfully, but it should not');
        Assert::same($this->client->getError(), 'code: Currency code must be unique.');
    }
}
