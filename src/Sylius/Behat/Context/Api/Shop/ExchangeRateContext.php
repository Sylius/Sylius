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
use Webmozart\Assert\Assert;

final class ExchangeRateContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I get exchange rates of the store
     */
    public function iGetExchangeRatesOfTheStore(): void
    {
        $this->client->index(Resources::EXCHANGE_RATES);
    }

    /**
     * @Then I should see :count exchange rates on the list
     */
    public function iShouldSeeExchangeRatesOnTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see that the exchange rate of :sourceCurrency to :targetCurrency is :ratio
     */
    public function iShouldSeeThatExchangeRateOfSourceCurrencyToTargetCurrencyIs(string $sourceCurrency, string $targetCurrency, float $ratio): void
    {
        $exchangeRate = $this->getExchangeRateByTargetCurrency($sourceCurrency, $targetCurrency);

        Assert::same($exchangeRate['ratio'], $ratio);
    }

    /**
     * @Then I should not see :sourceCurrency to :targetCurrency exchange rate
     */
    public function iShouldNotSeeSourceCurrencyToTargetCurrencyExchangeRate(string $sourceCurrency, string $targetCurrency): void
    {
        Assert::throws(
            fn () => $this->getExchangeRateByTargetCurrency($sourceCurrency, $targetCurrency),
            \RuntimeException::class,
            sprintf('Cannot find %s/%s exchange rate.', $sourceCurrency, $targetCurrency),
        );
    }

    private function getExchangeRateByTargetCurrency(string $sourceCurrencyCode, string $targetCurrencyCode): array
    {
        $exchangeRates = $this->responseChecker->getCollection($this->client->getLastResponse());

        foreach ($exchangeRates as $exchangeRate) {
            if (
                str_ends_with($exchangeRate['sourceCurrency'], $sourceCurrencyCode) &&
                str_ends_with($exchangeRate['targetCurrency'], $targetCurrencyCode)
            ) {
                return $exchangeRate;
            }
        }

        throw new \RuntimeException(sprintf('Cannot find %s/%s exchange rate.', $sourceCurrencyCode, $targetCurrencyCode));
    }
}
