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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    private array $shopBillingData = [];

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /**
     * @When I want to create a new channel
     */
    public function iWantToCreateANewChannel(): void
    {
        $this->client->buildCreateRequest(Resources::CHANNELS);
    }

    /**
     * @When I want to modify a channel :channel
     */
    public function iWantToModifyChannel(ChannelInterface $channel): void
    {
        $this->client->buildUpdateRequest(Resources::CHANNELS, $channel->getCode());
    }

    /**
     * @When I exclude the :taxon taxon from showing the lowest price of discounted products
     */
    public function iExcludeTheTaxonFromShowingTheLowestPriceOfDiscountedProducts(TaxonInterface $taxon): void
    {
        $this->iExcludeTheTaxonsFromShowingTheLowestPriceOfDiscountedProducts([$taxon]);
    }

    /**
     * @When /^I exclude the ("[^"]+" and "[^"]+" taxons) from showing the lowest price of discounted products$/
     */
    public function iExcludeTheTaxonsFromShowingTheLowestPriceOfDiscountedProducts(iterable $taxons): void
    {
        $taxonsIris = [];
        foreach ($taxons as $taxon) {
            $taxonsIris[] = $this->iriConverter->getIriFromItem($taxon);
        }

        $this->client->addRequestData('taxonsExcludedFromShowingLowestPrice', $taxonsIris);
    }

    /**
     * @When I specify its :field as :value
     * @When I :field it :value
     * @When I set its :field as :value
     * @When I define its :field as :value
     */
    public function iSpecifyItsAs(string $field, string $value): void
    {
        $this->client->addRequestData($field, $value);
    }

    /**
     * @When I choose :currency as the base currency
     */
    public function iChooseAsTheBaseCurrency(CurrencyInterface $currency): void
    {
        $this->client->addRequestData('baseCurrency', $this->iriConverter->getIriFromItemInSection($currency, 'admin'));
    }

    /**
     * @When I make it available in :locale
     */
    public function iMakeItAvailableInLocale(LocaleInterface $locale): void
    {
        $this->client->addRequestData('locales', [$this->iriConverter->getIriFromItem($locale)]);
    }

    /**
     * @When I choose :locale as a default locale
     */
    public function iChooseAsADefaultLocale(LocaleInterface $locale): void
    {
        $this->client->addRequestData('defaultLocale', $this->iriConverter->getIriFromItemInSection($locale, 'admin'));
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs(string $description): void
    {
        $this->client->addRequestData('description', $description);
    }

    /**
     * @When I set its contact email as :contactEmail
     */
    public function iSetItsContactEmailAs(string $contactEmail): void
    {
        $this->client->addRequestData('contactEmail', $contactEmail);
    }

    /**
     * @When I set its contact phone number as :contactPhoneNumber
     */
    public function iSetItsContactPhoneNumberAs(string $contactPhoneNumber): void
    {
        $this->client->addRequestData('contactPhoneNumber', $contactPhoneNumber);
    }

    /**
     * @When I choose :country and :otherCountry as operating countries
     */
    public function iChooseAndAsOperatingCountries(CountryInterface $country, CountryInterface $otherCountry): void
    {
        $this->client->addRequestData('countries', [
            $this->iriConverter->getIriFromItemInSection($country, 'admin'),
            $this->iriConverter->getIriFromItemInSection($otherCountry, 'admin'),
        ]);
    }

    /**
     * @When I allow to skip shipping step if only one shipping method is available
     */
    public function iAllowToSkipShippingStepIfOnlyOneShippingMethodIsAvailable(): void
    {
        $this->client->addRequestData('skippingShippingStepAllowed', true);
    }

    /**
     * @When I allow to skip payment step if only one payment method is available
     */
    public function iAllowToSkipPaymentStepIfOnlyOnePaymentMethodIsAvailable(): void
    {
        $this->client->addRequestData('skippingPaymentStepAllowed', true);
    }

    /**
     * @When I specify menu taxon as :taxon
     */
    public function iSpecifyMenuTaxonAs(TaxonInterface $taxon): void
    {
        $this->client->addRequestData('menuTaxon', $this->iriConverter->getIriFromItemInSection($taxon, 'admin'));
    }

    /**
     * @When I specify company as :company
     */
    public function iSpecifyCompanyAs(string $company): void
    {
        $this->shopBillingData['company'] = $company;
    }

    /**
     * @When I specify tax ID as :taxId
     */
    public function iSpecifyTaxIdAs(string $taxId): void
    {
        $this->shopBillingData['taxId'] = $taxId;
    }

    /**
     * @When I specify shop billing address as :street, :postcode :city, :country
     */
    public function specifyShopBillingAddressAs(
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country,
    ): void {
        $this->shopBillingData['street'] = $street;
        $this->shopBillingData['city'] = $city;
        $this->shopBillingData['postcode'] = $postcode;
        $this->shopBillingData['countryCode'] = $country->getCode();
    }

    /**
     * @When I select the :taxCalculationStrategy as tax calculation strategy
     */
    public function iSelectTaxCalculationStrategy(string $taxCalculationStrategy): void
    {
        $this->client->addRequestData('taxCalculationStrategy', StringInflector::nameToLowercaseCode($taxCalculationStrategy));
    }

    /**
     * @When /^I (enable|disable) showing the lowest price of discounted products$/
     */
    public function iEnableShowingTheLowestPriceOfDiscountedProducts(string $visible): void
    {
        $this->client->addRequestData('lowestPriceForDiscountedProductsVisible', $visible === 'enable');
    }

    /**
     * @When /^I specify (-?\d+) days as the lowest price for discounted products checking period$/
     */
    public function iSpecifyDaysAsTheLowestPriceForDiscountedProductsCheckingPeriod(int $days): void
    {
        $this->client->addRequestData('lowestPriceForDiscountedProductsCheckingPeriod', $days);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->setSubResourceData('shopBillingData', $this->shopBillingData);

        $this->client->create();
    }

    /**
     * @When I want to browse channels
     */
    public function iWantToBrowseChannels(): void
    {
        $this->client->index(Resources::CHANNELS);
    }

    /**
     * @When /^I choose (billing|shipping) address as a required address in the checkout$/
     */
    public function iChooseAddressAsARequiredAddressInTheCheckout(string $type): void
    {
        $this->client->addRequestData('shippingAddressInCheckoutRequired', $type === 'shipping');
    }

    /**
     * @When /^I want to modify (this channel)$/
     */
    public function iWantToModifyThisChannel(ChannelInterface $channel): void
    {
        $this->client->buildUpdateRequest(Resources::CHANNELS, $channel->getCode());
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($response = $this->client->getLastResponse()),
            'Channel could not be created: ' . $response->getContent(),
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
            'lowestPriceForDiscountedProductsCheckingPeriod',
        ));
    }

    /**
     * @Then /^the "[^"]+" channel should have the lowest price for discounted products checking period set to (\d+) days$/
     * @Then its lowest price for discounted products checking period should be set to :days days
     */
    public function theChannelShouldHaveTheLowestPriceForDiscountedProductsCheckingPeriodSetToDays(int $days): void
    {
        $lowestPriceForDiscountedProductsCheckingPeriod = $this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'lowestPriceForDiscountedProductsCheckingPeriod',
        );

        Assert::same($lowestPriceForDiscountedProductsCheckingPeriod, $days);
    }

    /**
     * @Then /^the ("[^"]+" channel) should have the lowest price of discounted products prior to the current discount (enabled|disabled)$/
     */
    public function theChannelShouldHaveTheLowestPriceOfDiscountedProductsPriorToTheCurrentDiscountEnabledOrDisabled(
        ChannelInterface $channel,
        string $visible,
    ): void {
        $lowestPriceForDiscountedProductsVisible = $this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'lowestPriceForDiscountedProductsVisible',
        );

        Assert::same($lowestPriceForDiscountedProductsVisible, $visible === 'enabled');
    }

    /**
     * @Then the channel :name should appear in the registry
     * @Then the channel :name should be in the registry
     */
    public function theChannelShouldAppearInTheRegistry(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::CHANNELS), 'name', $name),
            sprintf('Channel with name %s does not exist', $name),
        );
    }

    /**
     * @Then the channel :channel should have :taxon as a menu taxon
     */
    public function theChannelShouldHaveAsAMenuTaxon(ChannelInterface $channel, TaxonInterface $taxon): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'menuTaxon'),
            $this->iriConverter->getIriFromItemInSection($taxon, 'admin'),
            sprintf('Channel %s does not have %s menu taxon', $channel->getName(), $taxon->getName()),
        );
    }

    /**
     * @Then I should see :count channels in the list
     */
    public function iShouldSeeChannelsInTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then /^the required address in the checkout for this channel should be (billing|shipping)$/
     */
    public function theRequiredAddressInTheCheckoutForTheChannelShouldBe(string $type): void
    {
        Assert::true($this->responseChecker->hasValue(
            $this->client->getLastResponse(),
            'shippingAddressInCheckoutRequired',
            $type === 'shipping',
        ));
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Channel could not be edited',
        );
    }

    /**
     * @Then I should be notified that the lowest price for discounted products checking period must be lower
     */
    public function iShouldBeNotifiedThatTheLowestPriceForDiscountedProductsCheckingPeriodMustBeLower(): void
    {
        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->client->getLastResponse(),
            'Value must be less than 2147483647',
            'lowestPriceForDiscountedProductsCheckingPeriod',
        ));
    }

    /**
     * @Then /^this channel should have ("([^"]+)" and "([^"]+)" taxons) excluded from displaying the lowest price of discounted products$/
     */
    public function thisChannelShouldHaveTaxonsExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        iterable $taxons,
    ): void {
        $excludedTaxons = $this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'taxonsExcludedFromShowingLowestPrice',
        );

        foreach ($taxons as $taxon) {
            Assert::true($this->isResourceAdminIriInArray($taxon, $excludedTaxons));
        }
    }

    /**
     * @Then this channel should have :taxon taxon excluded from displaying the lowest price of discounted products
     */
    public function thisChannelShouldHaveTaxonExcludedFromDisplayingTheLowestPriceOfDiscountedProducts(
        TaxonInterface $taxon,
    ): void {
        $excludedTaxons = $this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'taxonsExcludedFromShowingLowestPrice',
        );

        Assert::true($this->isResourceAdminIriInArray($taxon, $excludedTaxons));
    }

    private function isResourceAdminIriInArray(ResourceInterface $resource, array $iris): bool
    {
        if (method_exists($this->iriConverter, 'getIriFromItemInSection')) {
            $iri = $this->iriConverter->getIriFromItemInSection($resource, 'admin');
        } else {
            $iri = $this->iriConverter->getIriFromItem($resource);
        }

        return in_array($iri, $iris, true);
    }
}
