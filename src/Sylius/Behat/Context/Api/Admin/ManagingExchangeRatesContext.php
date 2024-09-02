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
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Webmozart\Assert\Assert;

final class ManagingExchangeRatesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @Given /^I am editing (this exchange rate)$/
     * @When /^I want to edit (this exchange rate)$/
     */
    public function iWantToEditThisExchangeRate(ExchangeRateInterface $exchangeRate): void
    {
        $this->client->buildUpdateRequest(Resources::EXCHANGE_RATES, (string) $exchangeRate->getId());

        $this->sharedStorage->set('exchange_rate_id', (string) $exchangeRate->getId());
    }

    /**
     * @Given I am browsing exchange rates of the store
     * @When I browse exchange rates
     * @When I browse exchange rates of the store
     */
    public function iBrowseExchangeRatesOfTheStore(): void
    {
        $this->client->index(Resources::EXCHANGE_RATES);
    }

    /**
     * @When I want to add a new exchange rate
     */
    public function iWantToAddNewExchangeRate(): void
    {
        $this->client->buildCreateRequest(Resources::EXCHANGE_RATES);
    }

    /**
     * @When I specify its ratio as :ratio
     * @When I don't specify its ratio
     */
    public function iSpecifyItsRatioAs(?string $ratio = null): void
    {
        if ($ratio !== null) {
            $this->client->addRequestData('ratio', $ratio);
        }
    }

    /**
     * @When I choose :currencyCode as the source currency
     */
    public function iChooseAsTheSourceCurrency(string $currencyCode): void
    {
        $this->client->addRequestData(
            'sourceCurrency',
            sprintf('%s/admin/currencies/%s', $this->apiUrlPrefix, $currencyCode),
        );
    }

    /**
     * @When I choose :currencyCode as the target currency
     */
    public function iChooseAsTheTargetCurrency(string $currencyCode): void
    {
        $this->client->addRequestData(
            'targetCurrency',
            sprintf('%s/admin/currencies/%s', $this->apiUrlPrefix, $currencyCode),
        );
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I change ratio to :ratio
     */
    public function iChangeRatioTo(string $ratio): void
    {
        $this->client->updateRequestData(['ratio' => $ratio]);
    }

    /**
     * @When /^I delete the (exchange rate between "[^"]+" and "[^"]+")$/
     */
    public function iDeleteTheExchangeRateBetweenAnd(ExchangeRateInterface $exchangeRate): void
    {
        $this->client->delete(Resources::EXCHANGE_RATES, (string) $exchangeRate->getId());
    }

    /**
     * @When I choose :currency as a currency filter
     */
    public function iChooseCurrencyAsACurrencyFilter(CurrencyInterface $currency): void
    {
        $this->client->addFilter('currencyCode', $currency->getCode());
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter();
    }

    /**
     * @Then I should see :count exchange rates on the list
     */
    public function iShouldSeeExchangeRatesOnTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see a single exchange rate in the list
     * @Then I should( still) see one exchange rate on the list
     */
    public function iShouldSeeASingleExchangeRateInTheList(): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::EXCHANGE_RATES)), 1);
    }

    /**
     * @Then the exchange rate with ratio :ratio between :sourceCurrency and :targetCurrency should appear in the store
     */
    public function theExchangeRateWithRatioBetweenAndShouldAppearInTheStore(
        float $ratio,
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
    ): void {
        Assert::true(
            $this->responseHasExchangeRate($ratio, $sourceCurrency, $targetCurrency),
            sprintf(
                'Exchange rate with ratio %s between %s and %s does not exist',
                $ratio,
                $sourceCurrency->getName(),
                $targetCurrency->getName(),
            ),
        );
    }

    /**
     * @Then I should see the exchange rate between :sourceCurrency and :targetCurrency in the list
     * @Then I should (also) see an exchange rate between :sourceCurrency and :targetCurrency on the list
     */
    public function iShouldSeeTheExchangeRateBetweenAndInTheList(
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
    ): void {
        Assert::notNull(
            $this->getExchangeRateFromResponse($sourceCurrency, $targetCurrency),
            sprintf('Exchange rate for %s and %s currencies does not exist', $sourceCurrency, $targetCurrency),
        );
    }

    /**
     * @Then it should have a ratio of :ratio
     */
    public function itShouldHaveARatioOf(float $ratio): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::EXCHANGE_RATES), 'ratio', $ratio),
            sprintf('ExchangeRate with ratio %s does not exist', $ratio),
        );
    }

    /**
     * @Then /^(this exchange rate) should no longer be on the list$/
     */
    public function thisExchangeRateShouldNoLongerBeOnTheList(ExchangeRateInterface $exchangeRate): void
    {
        Assert::false(
            $this->responseHasExchangeRate(
                $exchangeRate->getRatio(),
                $exchangeRate->getSourceCurrency(),
                $exchangeRate->getTargetCurrency(),
            ),
            sprintf(
                'Exchange rate with ratio %s between %s and %s still exists, but it should not.',
                $exchangeRate->getRatio(),
                $exchangeRate->getSourceCurrency()->getName(),
                $exchangeRate->getTargetCurrency()->getName(),
            ),
        );
    }

    /**
     * @Then the exchange rate between :sourceCurrency and :targetCurrency should not be added
     */
    public function theExchangeRateBetweenAndShouldNotBeAdded(
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
    ): void {
        $this->client->index(Resources::EXCHANGE_RATES);

        Assert::null($this->getExchangeRateFromResponse($sourceCurrency, $targetCurrency));
    }

    /**
     * @Then /^(this exchange rate) should have a ratio of ([0-9\.]+)$/
     */
    public function thisExchangeRateShouldHaveARatioOf(ExchangeRateInterface $exchangeRate, float $ratio): void
    {
        $exchangeRate = $this->getExchangeRateFromResponse(
            $exchangeRate->getSourceCurrency(),
            $exchangeRate->getTargetCurrency(),
        );

        Assert::same($exchangeRate['ratio'], $ratio);
    }

    /**
     * @Then I should not be able to edit its source currency
     */
    public function iShouldNotBeAbleToEditItsSourceCurrency(): void
    {
        $this->assertIfNotBeAbleToEditItCurrency('sourceCurrency');
    }

    /**
     * @Then I should not be able to edit its target currency
     */
    public function iShouldNotBeAbleToEditItsTargetCurrency(): void
    {
        $this->assertIfNotBeAbleToEditItCurrency('targetCurrency');
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter exchange rate %s.', $element, $element),
        );
    }

    /**
     * @Then I should be notified that the ratio must be greater than zero
     */
    public function iShouldBeNotifiedThatRatioMustBeGreaterThanZero(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The ratio must be greater than 0.',
        );
    }

    /**
     * @Then I should be notified that source and target currencies must differ
     */
    public function iShouldBeNotifiedThatSourceAndTargetCurrenciesMustDiffer(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The source and target currencies must differ.',
        );
    }

    /**
     * @Then I should be notified that the currency pair must be unique
     */
    public function iShouldBeNotifiedThatTheCurrencyPairMustBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The currency pair must be unique.',
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Exchange rate could not be created',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Exchange rate could not be deleted',
        );
    }

    private function assertIfNotBeAbleToEditItCurrency(string $currencyType): void
    {
        $this->client->buildUpdateRequest(Resources::EXCHANGE_RATES, $this->sharedStorage->get('exchange_rate_id'));

        $this->client->addRequestData(
            $currencyType,
            sprintf('%s/admin/currencies/EUR', $this->apiUrlPrefix),
        );
        $this->client->update();

        Assert::false(
            $this->responseChecker->hasItemOnPositionWithValue(
                $this->client->index(Resources::EXCHANGE_RATES),
                0,
                $currencyType,
                sprintf('%s/admin/currencies/EUR', $this->apiUrlPrefix),
            ),
            sprintf('It was possible to change %s', $currencyType),
        );
    }

    private function getExchangeRateFromResponse(
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
    ): ?array {
        /** @var array $item */
        foreach ($this->responseChecker->getCollection($this->client->index(Resources::EXCHANGE_RATES)) as $item) {
            if (
                $item['sourceCurrency'] === sprintf('%s/admin/currencies/%s', $this->apiUrlPrefix, $sourceCurrency->getCode()) &&
                $item['targetCurrency'] === sprintf('%s/admin/currencies/%s', $this->apiUrlPrefix, $targetCurrency->getCode())
            ) {
                return $item;
            }
        }

        return null;
    }

    private function responseHasExchangeRate(
        float $ratio,
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
    ): bool {
        $exchangeRateResponse = $this->getExchangeRateFromResponse($sourceCurrency, $targetCurrency);

        if (null === $exchangeRateResponse) {
            return false;
        }

        return $exchangeRateResponse['ratio'] === $ratio;
    }
}
