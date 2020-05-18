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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ApiClientInterface  */
    private $shopBillingDataClient;

    /** @var CountryNameConverterInterface */
    private $countryNameConverter;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var array */
    private $shopBillingData = [];

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ApiClientInterface $shopBillingDataClient,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage,
        CountryNameConverterInterface $countryNameConverter
    ) {
        $this->client = $client;
        $this->shopBillingDataClient = $shopBillingDataClient;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
        $this->countryNameConverter = $countryNameConverter;
    }

    /**
     * @When I browse channels
     */
    public function iBrowseChannels(): void
    {
        $this->client->index();
    }

    /**
     * @When I check the :channel channel
     * @When I check also the :channel channel
     */
    public function iCheckTheChannel(ChannelInterface $channel): void
    {
        $channelToDelete = [];
        if ($this->sharedStorage->has('channel_to_delete')) {
            $channelToDelete = $this->sharedStorage->get('channel_to_delete');
        }
        $channelToDelete[] = $channel->getCode();
        $this->sharedStorage->set('channel_to_delete', $channelToDelete);
    }

    /**
     * @When I want to create a new channel
     */
    public function iWantToCreateANewChannel(): void
    {
        $this->client->buildCreateRequest();
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
        $this->client->addRequestData('baseCurrency', $this->iriConverter->getIriFromItem($currency));
    }

    /**
     * @When I choose :locale as a default locale
     */
    public function iChooseAsADefaultLocale(LocaleInterface $locale): void
    {
        $this->client->addRequestData('defaultLocale', $this->iriConverter->getIriFromItem($locale));
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
     * @When I choose :country and :otherCountry as operating countries
     */
    public function iChooseAndAsOperatingCountries(CountryInterface $country, CountryInterface $otherCountry): void
    {
        $this->client->addRequestData('countries', [
            $this->iriConverter->getIriFromItem($country),
            $this->iriConverter->getIriFromItem($otherCountry),
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
     * @When I change its menu taxon to :taxon
     */
    public function iSpecifyMenuTaxonAs(TaxonInterface $taxon): void
    {
        $this->client->addRequestData('menuTaxon', $this->iriConverter->getIriFromItem($taxon));
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
        CountryInterface $country
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
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->addSubResourceData('shopBillingData', $this->shopBillingData);

        $this->client->create();
    }

    /**
     * @When I want to browse channels
     */
    public function iWantToBrowseChannels(): void
    {
        $this->client->index();
    }

    /**
     * @When I delete channel :channel
     */
    public function iDeleteChannel(ChannelInterface $channel): void
    {
        $this->client->delete($channel->getCode());
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        foreach ($this->sharedStorage->get('channel_to_delete') as $code) {
            $this->client->delete($code);
        }
    }

    /**
     * @Given I am modifying a channel :channel
     * @When I want to modify a channel :channel
     * @When /^I want to modify (this channel)$/
     */
    public function iWantToModifyAProduct(ChannelInterface $channel): void
    {
        $this->client->buildUpdateRequest($channel->getCode());
    }

    /**
     * @When I rename it to :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt(?string $name): void
    {
        $this->client->addRequestData('name', $name ?? '');
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I save my change of shop billing data
     */
    public function iSaveMyChangeOfShopBillingData(): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');

        Assert::isInstanceOf($channel->getShopBillingData(), ShopBillingData::class);

        $this->shopBillingDataClient->buildUpdateRequest((string) $channel->getShopBillingData()->getId());
        $this->shopBillingDataClient->updateRequestData($this->shopBillingData);
        $this->shopBillingDataClient->update();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Channel could not be created'
        );
    }

    /**
     * @Then the channel :name should appear in the registry
     * @Then the channel :name should be in the registry
     */
    public function theChannelShouldAppearInTheRegistry(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('Channel with name %s does not exist', $name)
        );
    }

    /**
     * @Then the channel :channel should have :name as a menu taxon
     * @Then /^(this channel) menu (taxon should be "([^"]+)")$/
     */
    public function theChannelShouldHaveAsAMenuTaxon(ChannelInterface $channel, TaxonInterface $taxon): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show($channel->getCode()), 'menuTaxon'),
            $this->iriConverter->getIriFromItem($taxon),
            sprintf('Channel %s does not have %s menu taxon', $channel->getName(), $taxon->getName())
        );
    }

    /**
     * @Then I should see :count channels in the list
     * @Then I should see a single channel in the list
     */
    public function iShouldSeeChannelsInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index()), $count);
    }

    /**
     * @Then I should see the channel :name in the list
     */
    public function iShouldSeeTheChannelInTheList(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('There is no channel with name "%s"', $name)
        );
    }

    /**
     * @Then the :name channel should no longer exist in the registry
     */
    public function thisProductAssociationTypeShouldNoLongerExistInTheRegistry(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('Channel with name %s exist', $name)
        );
    }

    /**
     * @Then /^(this channel) name should be "([^"]+)"$/
     * @Then /^(this channel) should still be named "([^"]+)"$/
     */
    public function thisChannelNameShouldBe(ChannelInterface $channel, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show($channel->getCode()), 'name', $name),
            sprintf('Channel with name %s does not exist', $name)
        );
    }

    /**
     * @Then /^(this channel) company should be "([^"]+)"$/
     */
    public function thisChannelCompanyShouldBe(ChannelInterface $channel, string $company): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->subResourceIndex('shop-billing-data', $channel->getCode()), 'company', $company),
            sprintf('Channel "%s" as not "%s" as company name.', $channel->getName(), $company)
        );
    }

    /**
     * @Then /^(this channel) tax ID should be "([^"]+)"$/
     */
    public function thisChannelTaxIdShouldBe2(ChannelInterface $channel, string $taxId): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->subResourceIndex('shop-billing-data', $channel->getCode()), 'taxId', $taxId),
            sprintf('Channel "%s" as not "%s" as tax ID.', $channel->getName(), $taxId)
        );
    }

    /**
     * @Then /^(this channel) shop billing address should be "([^"]+)", "([^"]+)" "([^"]+)", "([^"]+)"$/
     */
    public function thisChannelShopBillingAddressShouldBe(ChannelInterface $channel, string $street, string $postcode, string $city, string $country)
    {
        $this->client->subResourceIndex('shop-billing-data', $channel->getCode());
        Assert::true(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'street', $street),
            sprintf('Channel "%s" as not "%s" as street.', $channel->getName(), $street)
        );
        Assert::true(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'postcode', $postcode),
            sprintf('Channel "%s" as not "%s" as postcode.', $channel->getName(), $postcode)
        );
        Assert::true(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'city', $city),
            sprintf('Channel "%s" as not "%s" as city.', $channel->getName(), $city)
        );

        $countryCode = $this->countryNameConverter->convertToCode($country);
        Assert::true(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'countryCode', $countryCode),
            sprintf('Channel "%s" as not "%s" as country.', $channel->getName(), $country)
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     * @Then I should be notified that they have been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful(
            $this->client->getLastResponse()),
            'Channel could not be deleted'
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Channel could not be edited'
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', 'NEW_CODE');

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The code field with value NEW_CODE exist'
        );
    }

    /**
     * @Then I should not be able to edit its base currency
     */
    public function iShouldNotBeAbleToEditItsBaseCurrency(): void
    {
        $this->client->addRequestData('baseCurrency', 'NEW_CURRENCY');

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'baseCurrency', 'NEW_CURRENCY'),
            'The base currency field with value NEW_CODE exist'
        );
    }
}
