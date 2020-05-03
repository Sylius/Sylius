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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $billingDataClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var array */
    private $billingData = [];

    public function __construct(
        ApiClientInterface $client,
        ApiClientInterface $billingDataClient,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->billingDataClient = $billingDataClient;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @Given I want to create a new channel
     */
    public function iWantToCreateANewChannel(): void
    {
        $this->client->buildCreateRequest();

        // @see https://github.com/Sylius/Sylius/issues/11414
        $this->client->addRequestData('taxCalculationStrategy', 'order_items_based');
    }

    /**
     * @When I specify its :field as :value
     * @When I :field it :value
     * @When I set its :field as :value
     * @When I define its :field as :value
     */
    public function iSpecifyItsCodeAs(string $field, string $value): void
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
        $this->billingData['company'] = $company;
    }

    /**
     * @When I specify tax ID as :taxId
     */
    public function iSpecifyTaxIdAs(string $taxId): void
    {
        $this->billingData['taxId'] = $taxId;
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
        $this->billingData['street'] = $street;
        $this->billingData['city'] = $city;
        $this->billingData['postcode'] = $postcode;
        $this->billingData['countryCode'] = $country->getCode();
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->createBillingData();

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
            $this->responseChecker->hasItemWithValue($this->client->index(),'name', $name),
            sprintf('Channel with name %s does not exist', $name)
        );
    }

    /**
     * @Then the channel :channel should have :taxon as a menu taxon
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
     */
    public function iShouldSeeChannelsInTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    private function createBillingData(): void
    {
        if (!empty($this->billingData)) {
            $this->billingDataClient->buildCreateRequest();
            foreach ($this->billingData as $field => $value) {
                $this->billingDataClient->addRequestData($field, $value);
            }
            $this->billingDataClient->create();
            $iri = $this->responseChecker->getValue($this->billingDataClient->getLastResponse(), '@id');
            $this->client->addRequestData('shopBillingData', $iri);
        }
    }
}
