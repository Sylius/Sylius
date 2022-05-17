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
        private ResponseCheckerInterface $responseChecker
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
     * @Then I should see that the exchange rate for :currency is :count
     */
    public function iShouldSeeThatExchangeRateForCurrencyIs(string $currency, int $count): void
    {
        // TODO: I should see that the exchange rate for :currency is :count
    }
}
