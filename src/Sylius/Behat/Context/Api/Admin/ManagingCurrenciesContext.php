<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Webmozart\Assert\Assert;

final class ManagingCurrenciesContext implements Context
{
    /** @var AbstractBrowser */
    private $client;

    public function __construct(AbstractBrowser $client)
    {
        $this->client = $client;
    }

    /**
     * @When I want to browse currencies of the store
     */
    public function iWantToSeeAllCurrenciesInStore(): void
    {
        $this->client->request('GET', '/new-api/currencies', [], [], ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    /**
     * @Then I should see :count currencies in the list
     */
    public function iShouldSeeCurrenciesInTheList(int $count): void
    {
        $response = $this->getResponseContent();

        Assert::eq($count, count($response['hydra:member']));
    }

    /**
     * @Then I should see the currency :currencyName in the list
     */
    public function currencyShouldAppearInTheStore(string $currencyName): void
    {
        $currencies = $this->getResponseContent()['hydra:member'];

        foreach ($currencies as $currency) {
            if ($currency['name'] === $currencyName) {
                return;
            }
        }

        throw new \Exception(sprintf('There is not currency "%s" in the list', $currencyName));
    }

    private function getResponseContent(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
