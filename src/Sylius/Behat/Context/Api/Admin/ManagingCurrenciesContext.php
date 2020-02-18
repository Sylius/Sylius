<?php

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
    }

    /**
     * @When I want to browse currencies of the store
     */
    public function iWantToSeeAllCurrenciesInStore(): void
    {
        $this->client->index('currencies');
    }

    /**
     * @Then I should see :count currencies in the list
     */
    public function iShouldSeeCurrenciesInTheList(int $count): void
    {
        Assert::eq($count, $this->client->countCollectionItems());
    }

    /**
     * @Then I should see the currency :currencyName in the list
     */
    public function currencyShouldAppearInTheStore(string $currencyName): void
    {
        $currencies = $this->client->getCollection();

        foreach ($currencies as $currency) {
            if ($currency['name'] === $currencyName) {
                return;
            }
        }

        throw new \Exception(sprintf('There is not currency "%s" in the list', $currencyName));
    }
}
