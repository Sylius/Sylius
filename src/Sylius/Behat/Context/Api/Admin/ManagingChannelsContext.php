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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    use ValidationTrait;

    private array $shopBillingData = [];

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
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
     * @When I delete channel :channel
     */
    public function iDeleteChannel(ChannelInterface $channel): void
    {
        $this->client->delete(Resources::CHANNELS, $channel->getCode());
    }

    /**
     * @When I want to modify a channel :channel
     */
    public function iWantToModifyChannel(ChannelInterface $channel): void
    {
        $this->client->buildUpdateRequest(Resources::CHANNELS, $channel->getCode());
    }

    /**
     * @When I rename it to :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iRenameIt(string $name = ''): void
    {
        $this->client->addRequestData('name', $name);
    }

    /**
     * @When /^I (enable|disable) it$/
     */
    public function iDisableIt(string $toggleAction): void
    {
        $this->client->addRequestData('enabled', $toggleAction === 'enable');
    }

    /**
     * @When I change its menu taxon to :taxon
     */
    public function iChangeItsMenuTaxonTo(TaxonInterface $taxon): void
    {
        $this->client->addRequestData('menuTaxon', $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'));
    }

    /**
     * @When I specify its :field as :value
     * @When I :field it :value
     * @When I set its :field as :value
     * @When I define its :field as :value
     * @When I do not specify its :field
     */
    public function iSpecifyItsAs(string $field, string $value = ''): void
    {
        $this->client->addRequestData($field, $value);
    }

    /**
     * @When I choose :currency as the base currency
     * @When I do not choose base currency
     */
    public function iChooseAsTheBaseCurrency(?CurrencyInterface $currency = null): void
    {
        $this->client->addRequestData(
            'baseCurrency',
            null === $currency ? $currency : $this->sectionAwareIriConverter->getIriFromResourceInSection($currency, 'admin'),
        );
    }

    /**
     * @When I allow for paying in :currency
     */
    public function iAllowToPayingForThisChannel(CurrencyInterface $currency): void
    {
        $this->client->addRequestData('currencies', [$this->iriConverter->getIriFromResource($currency)]);
    }

    /**
     * @When I select the :zone as default tax zone
     */
    public function iSelectDefaultTaxZone(ZoneInterface $zone): void
    {
        $this->client->addRequestData('defaultTaxZone', $this->iriConverter->getIriFromResource($zone));
    }

    /**
     * @When I remove its default tax zone
     */
    public function iRemoveItsDefaultTaxZone(): void
    {
        $this->client->addRequestData('defaultTaxZone', null);
    }

    /**
     * @When I make it available in :locale
     */
    public function iMakeItAvailableInLocale(LocaleInterface $locale): void
    {
        $this->client->addRequestData('locales', [$this->sectionAwareIriConverter->getIriFromResourceInSection($locale, 'admin')]);
    }

    /**
     * @When I make it available only in :locale
     */
    public function iMakeItAvailableOnlyInLocale(LocaleInterface $locale): void
    {
        $this->client->replaceRequestData('locales', [$this->sectionAwareIriConverter->getIriFromResourceInSection($locale, 'admin')]);
    }

    /**
     * @When I choose :locale as a default locale
     * @When I do not choose default locale
     */
    public function iChooseAsADefaultLocale(?LocaleInterface $locale = null): void
    {
        $this->client->addRequestData(
            'defaultLocale',
            null === $locale ? $locale : $this->sectionAwareIriConverter->getIriFromResourceInSection($locale, 'admin'),
        );
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
            $this->sectionAwareIriConverter->getIriFromResourceInSection($country, 'admin'),
            $this->sectionAwareIriConverter->getIriFromResourceInSection($otherCountry, 'admin'),
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
        $this->client->addRequestData('menuTaxon', $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'));
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
     * @When /^I specify shop billing data for (this channel) as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" tax ID and ("([^"]+)" country)$/
     */
    public function iSpecifyShopBillingDataAs(
        ChannelInterface $channel,
        string $company,
        string $street,
        string $postcode,
        string $city,
        string $taxId,
        CountryInterface $country,
    ): void {
        $shopBillingDataId = $this->iriConverter->getIriFromResource($channel->getShopBillingData());

        $this->client->addRequestData(
            'shopBillingData',
            [
                '@id' => $shopBillingDataId,
                'company' => $company,
                'street' => $street,
                'postcode' => $postcode,
                'city' => $city,
                'countryCode' => $country->getCode(),
                'taxId' => $taxId,
            ],
        );
    }

    /**
     * @When /^I specify new country code for (this channel) as "([^"]+)"$/
     */
    public function iSpecifyNewCountryCodeForThisChannelAs(ChannelInterface $channel, string $code): void
    {
        $shopBillingDataId = $this->iriConverter->getIriFromResource($channel->getShopBillingData());

        $this->client->addRequestData('shopBillingData', ['@id' => $shopBillingDataId, 'countryCode' => $code]);
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
     * @Then I save it
     */
    public function iSaveIt(): void
    {
        $this->iAddIt();
        $this->client->update();
    }

    /**
     * @When I select the :taxCalculationStrategy as tax calculation strategy
     */
    public function iSelectTaxCalculationStrategy(string $taxCalculationStrategy): void
    {
        $this->client->addRequestData('taxCalculationStrategy', StringInflector::nameToLowercaseCode($taxCalculationStrategy));
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
     * @When /^I specify its ([^"]+) as a too long string$/
     */
    public function iSpecifyItsFieldAsATooLongString(string $field): void
    {
        $this->client->addRequestData(StringInflector::nameToCamelCase($field), str_repeat('a@', 128));
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
            $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'),
            sprintf('Channel %s does not have %s menu taxon', $channel->getName(), $taxon->getName()),
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then the base currency field should be disabled
     * @Then I should not be able to edit its base currency
     */
    public function theBaseCurrencyFieldShouldBeDisabled(): void
    {
        $this->client->updateRequestData(['baseCurrency' => 'PLN']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'baseCurrency', 'PLN'));
    }

    /**
     * @Then /^(this channel) name should be "([^"]*)"$/
     */
    public function thisChannelNameShouldBe(ChannelInterface $channel, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'name', $name),
            sprintf('Its Channel does not have name %s.', $name),
        );
    }

    /**
     * @Then the :channel channel should no longer exist in the registry
     */
    public function theChannelShouldNoLongerExistInTheRegistry(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::CHANNELS), 'name', $name),
            sprintf('Channel with name %s exists', $name),
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatChannelHasBeenDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then /^(this channel) menu (taxon should be "([^"]+)")$/
     */
    public function thisChannelMenuTaxonShouldBe(ChannelInterface $channel, TaxonInterface $taxon): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(
                    Resources::CHANNELS,
                    $channel->getCode(),
                ),
                'menuTaxon',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'),
            ),
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
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The channel cannot be deleted. At least one enabled channel is required.',
        );
    }

    /**
     * @Then I should be notified that at least one channel has to be defined
     */
    public function iShouldBeNotifiedThatAtLeastOneChannelHasToBeDefined(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Must have at least one enabled entity',
        );
    }

    /**
     * @Then channel with name :channel should still be enabled
     * @Then /^(this channel) should be enabled$/
     */
    public function channelWithNameShouldStillBeEnabled(ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'enabled', true),
            sprintf('Channel with name %s does not exists', $channel->getName()),
        );
    }

    /**
     * @Then this channel should still be named :channel
     */
    public function thisChannelShouldStillBeNamed(ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'name', $channel->getName()),
            sprintf('Channel with name %s does not exists', $channel->getName()),
        );
    }

    /**
     * @Then paying in :currency should be possible for the :channel channel
     */
    public function payingInCurrencyShouldBePossibleForTheChannel(CurrencyInterface $currency, ChannelInterface $channel): void
    {
        $currencies = $this->responseChecker->getValue(
            $this->client->show(Resources::CHANNELS, $channel->getCode()),
            'currencies',
        );

        Assert::true(in_array($this->sectionAwareIriConverter->getIriFromResourceInSection($currency, 'admin'), $currencies));
    }

    /**
     * @Then channel :channel should not have default tax zone
     */
    public function channelShouldNotHaveDefaultTaxZone(ChannelInterface $channel): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'defaultTaxZone'),
            null,
            sprintf('Channel %s has default tax zone', $channel->getName()),
        );
    }

    /**
     * @Then the default tax zone for the :channel channel should be :zone
     */
    public function theDefaultTaxZoneForTheChannelShouldBe(ChannelInterface $channel, ZoneInterface $zone): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'defaultTaxZone'),
            $this->sectionAwareIriConverter->getIriFromResourceInSection($zone, 'admin'),
            sprintf('Channel %s does not have %s default tax zone', $channel->getName(), $zone),
        );
    }

    /**
     * @Then the channel :channel should be available in :locale
     */
    public function theChannelShouldBeAvailableIn(ChannelInterface $channel, LocaleInterface $locale): void
    {
        $locales = $this->responseChecker->getValue(
            $this->client->show(Resources::CHANNELS, $channel->getCode()),
            'locales',
        );

        Assert::true(in_array($this->sectionAwareIriConverter->getIriFromResourceInSection($locale, 'admin'), $locales));
    }

    /**
     * @Then I should be notified that the default locale has to be enabled
     */
    public function iShouldBeNotifiedThatTheDefaultLocaleHasToBeEnabled(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'defaultLocale: Default locale has to be enabled.',
        );
    }

    /**
     * @Then /^(this channel) should still be in the registry$/
     */
    public function thisChannelShouldStillBeInTheRegistry(ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::CHANNELS), 'code', $channel->getCode()),
            sprintf('Channel with code %s does not exists', $channel->getCode()),
        );
    }

    /**
     * @Then the tax calculation strategy for the :channel channel should be :taxCalculationStrategy
     */
    public function theTaxCalculationStrategyForTheChannelShouldBe(
        ChannelInterface $channel,
        string $taxCalculationStrategy,
    ): void {
        Assert::same(
            $this->responseChecker->getValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'taxCalculationStrategy'),
            StringInflector::nameToLowercaseCode($taxCalculationStrategy),
            sprintf('Channel %s does not have %s tax calculation strategy', $channel->getName(), $taxCalculationStrategy),
        );
    }

    /**
     * @Then /^(this channel) should be disabled$/
     */
    public function thisChannelShouldBeDisabled(ChannelInterface $channel): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show(Resources::CHANNELS, $channel->getCode()), 'enabled'),
            false,
            sprintf('Channel %s is enabled', $channel->getName()),
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter channel %s.', $element, $element),
        );
    }

    /**
     * @Then I should be notified that base currency is required
     */
    public function iShouldBeNotifiedThatBaseCurrencyIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Expected IRI or nested document for attribute "baseCurrency", "NULL" given.',
        );
    }

    /**
     * @Then I should be notified that default locale is required
     */
    public function iShouldBeNotifiedThatDefaultLocaleIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Expected IRI or nested document for attribute "defaultLocale", "NULL" given.',
        );
    }

    /**
     * @Then channel with :element :value should not be added
     */
    public function channelWithShouldNotBeAdded(string $element, string $value): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::CHANNELS), $element, $value),
            sprintf('Channel with %s: %s exists', $element, $value),
        );
    }

    /**
     * @Then I should be notified that channel with this code already exists
     */
    public function iShouldBeNotifiedThatChannelWithThisCodeAlreadyExists(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'code: Channel code has to be unique.',
        );
    }

    /**
     * @Then there should still be only one channel with :element :value
     */
    public function thereShouldStillBeOnlyOneChannelWithCode(string $element, string $value): void
    {
        Assert::same(
            count($this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::CHANNELS), $element, $value)),
            1,
            sprintf('There is more than one channel with %s: %s', $element, $value),
        );
    }

    /**
     * @Then I should be notified that it is not a valid country
     */
    public function iShouldBeNotifiedThatItIsNotAValidCountryCode(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'countryCode: This value is not a valid country.',
        );
    }
}
