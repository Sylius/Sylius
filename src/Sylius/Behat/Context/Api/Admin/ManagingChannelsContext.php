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
     * @When I add it
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
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Channel could not be created',
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
}
