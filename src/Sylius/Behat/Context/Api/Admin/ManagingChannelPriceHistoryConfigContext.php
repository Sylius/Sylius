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
use Sylius\Behat\Service\Converter\IriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelPriceHistoryConfigContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When /^I (enable|disable) showing the lowest price of discounted products$/
     */
    public function iEnableShowingTheLowestPriceOfDiscountedProducts(string $visible): void
    {
        $this->client->addRequestData(
            'channelPriceHistoryConfig',
            ['lowestPriceForDiscountedProductsVisible' => $visible === 'enable'],
        );
    }

    /**
     * @When /^I specify (-?\d+) days as the lowest price for discounted products checking period$/
     */
    public function iSpecifyDaysAsTheLowestPriceForDiscountedProductsCheckingPeriod(int $days): void
    {
        $this->client->addRequestData(
            'channelPriceHistoryConfig',
            ['lowestPriceForDiscountedProductsCheckingPeriod' => $days],
        );
    }

    /**
     * @When I exclude the :taxon taxon from showing the lowest price of discounted products
     */
    public function iExcludeTheTaxonFromShowingTheLowestPriceOfDiscountedProducts(TaxonInterface $taxon): void
    {
        $this->iExcludeTheTaxonsFromShowingTheLowestPriceOfDiscountedProducts([$taxon]);
    }

    /**
     * @When I remove the :taxon taxon from excluded taxons from showing the lowest price of discounted products
     */
    public function iRemoveTheTaxonFromExcludedTaxonsFromShowingTheLowestPriceOfDiscountedProducts(TaxonInterface $taxon): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');

        $leftTaxons = [];
        foreach ($channel->getChannelPriceHistoryConfig()->getTaxonsExcludedFromShowingLowestPrice() as $excludedTaxon) {
            if ($excludedTaxon->getId() !== $taxon->getId()) {
                $leftTaxons[] = $excludedTaxon;
            }
        }

        $this->iExcludeTheTaxonsFromShowingTheLowestPriceOfDiscountedProducts($leftTaxons);
    }

    /**
     * @When /^I exclude the ("[^"]+" and "[^"]+" taxons) from showing the lowest price of discounted products$/
     */
    public function iExcludeTheTaxonsFromShowingTheLowestPriceOfDiscountedProducts(iterable $taxons): void
    {
        $taxonsIris = [];
        foreach ($taxons as $taxon) {
            $taxonsIris[] = $this->iriConverter->getIriFromResource($taxon);
        }

        $this->client->addRequestData(
            'channelPriceHistoryConfig',
            ['taxonsExcludedFromShowingLowestPrice' => $taxonsIris],
        );
    }

    /**
     * @Then /^the "[^"]+" channel should have the lowest price of discounted products prior to the current discount (enabled|disabled)$/
     */
    public function theChannelShouldHaveTheLowestPriceOfDiscountedProductsPriorToTheCurrentDiscountEnabledOrDisabled(
        string $visible,
    ): void {
        Assert::same(
            $this->getChannelPricingFieldFromLastResponse('lowestPriceForDiscountedProductsVisible'),
            $visible === 'enabled',
        );
    }

    /**
     * @Then /^the "[^"]+" channel should have the lowest price for discounted products checking period set to (\d+) days$/
     * @Then its lowest price for discounted products checking period should be set to :days days
     */
    public function theChannelShouldHaveTheLowestPriceForDiscountedProductsCheckingPeriodSetToDays(int $days): void
    {
        Assert::same(
            $this->getChannelPricingFieldFromLastResponse('lowestPriceForDiscountedProductsCheckingPeriod'),
            $days,
        );
    }

    /**
     * @Then I should be notified that the lowest price for discounted products checking period must be greater than 0
     */
    public function iShouldBeNotifiedThatTheLowestPriceForDiscountedProductsCheckingPeriodMustBeGreaterThanZero(): void
    {
        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->client->getLastResponse(),
            'Value must be greater than 0',
            'channelPriceHistoryConfig.lowestPriceForDiscountedProductsCheckingPeriod',
        ));
    }

    /**
     * @Then I should be notified that the lowest price for discounted products checking period must be lower
     */
    public function iShouldBeNotifiedThatTheLowestPriceForDiscountedProductsCheckingPeriodMustBeLower(): void
    {
        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->client->getLastResponse(),
            'Value must be less than 2147483647',
            'channelPriceHistoryConfig.lowestPriceForDiscountedProductsCheckingPeriod',
        ));
    }

    /**
     * @Then /^this channel should have ("[^"]+" taxon) excluded from displaying the lowest price of discounted products$/
     */
    public function thisChannelShouldHaveTaxonExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        TaxonInterface $taxon,
    ): void {
        $this->thisChannelShouldHaveTaxonsExcludedFromDisplayingTheLowestPriceOfDiscountedProducts([$taxon]);
    }

    /**
     * @Then /^this channel should have ("([^"]+)" and "([^"]+)" taxons) excluded from displaying the lowest price of discounted products$/
     */
    public function thisChannelShouldHaveTaxonsExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        iterable $taxons,
    ): void {
        $excludedTaxons = $this->getChannelPricingFieldFromLastResponse('taxonsExcludedFromShowingLowestPrice', []);

        foreach ($taxons as $taxon) {
            Assert::true($this->isResourceAdminIriInArray($taxon, $excludedTaxons));
        }
    }

    /**
     * @Then /^this channel should not have ("[^"]+" taxon) excluded from displaying the lowest price of discounted products$/
     */
    public function thisChannelShouldNotHaveTaxonExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        TaxonInterface $taxon,
    ): void {
        $excludedTaxons = $this->getChannelPricingFieldFromLastResponse('taxonsExcludedFromShowingLowestPrice', []);

        Assert::false($this->isResourceAdminIriInArray($taxon, $excludedTaxons));
    }

    private function getChannelPricingFieldFromLastResponse(string $field, ?array $default = null): bool|int|string|array|null
    {
        return $this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'channelPriceHistoryConfig',
        )[$field] ?? $default;
    }

    private function isResourceAdminIriInArray(ResourceInterface $resource, array $iris): bool
    {
        $iri = $this->iriConverter->getIriFromResourceInSection($resource, 'admin');

        return in_array($iri, $iris, true);
    }
}
