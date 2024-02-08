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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;

final class CurrencyContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When /^I (?:start browsing|try to browse|browse) currencies$/
     */
    public function iBrowseCurrencies(): void
    {
        $this->client->index(Resources::CURRENCIES);
    }

    /**
     * @Then I should see :firstCurrency in the list
     * @Then I should see :firstCurrency and :secondCurrency in the list
     * @Then I should see :firstCurrency, :secondCurrency and :thirdCurrency in the list
     */
    public function iShouldSeeCurrenciesInTheList(string ...$currenciesCodes): void
    {
        $response = $this->client->getLastResponse();

        foreach ($currenciesCodes as $currencyCode) {
            $this->responseChecker->getCollectionItemsWithValue($response, 'code', $currencyCode);
        }
    }
}
