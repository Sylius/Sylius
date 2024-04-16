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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class ChannelContext implements Context
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ChannelContextSetterInterface $channelContextSetter,
        private DefaultChannelFactoryInterface $unitedStatesChannelFactory,
        private DefaultChannelFactoryInterface $defaultChannelFactory,
        private ChannelRepositoryInterface $channelRepository,
        private ObjectManager $channelManager,
    ) {
    }

    /**
     * @Given :channel channel has account verification disabled
     */
    public function channelHasAccountVerificationDisabled(ChannelInterface $channel): void
    {
        $channel->setAccountVerificationRequired(false);

        $this->channelManager->flush();
    }

    /**
     * @Given /^the (channel "[^"]+") has ("([^"]+)" and "([^"]+)" taxons) excluded from showing the lowest price of discounted products$/
     */
    public function theTaxonAndTaxonAreExcludedFromShowingTheLowestPriceOfDiscountedProductsOnThisChannel(
        ChannelInterface $channel,
        iterable $taxons,
    ): void {
        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $channel->getChannelPriceHistoryConfig()->addTaxonExcludedFromShowingLowestPrice($taxon);
        }

        $this->channelManager->flush();
    }

    /**
     * @Given the store operates on a single channel in "United States"
     */
    public function storeOperatesOnASingleChannelInUnitedStates(): void
    {
        $defaultData = $this->unitedStatesChannelFactory->create();

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given the store operates on a single channel in the "United States" named :channelName
     */
    public function storeOperatesOnASingleChannelInTheUnitedStatesNamed(string $channelName): void
    {
        $channelCode = StringInflector::nameToLowercaseCode($channelName);
        $defaultData = $this->unitedStatesChannelFactory->create($channelCode, $channelName);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given the store operates on a single channel
     * @Given the store operates on a single channel in :currencyCode currency
     */
    public function storeOperatesOnASingleChannel(?string $currencyCode = null): void
    {
        $defaultData = $this->defaultChannelFactory->create(null, null, $currencyCode);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given the store operates on a single channel in :localeCode locale
     */
    public function storeOperatesOnASingleChannelInLocale(string $localeCode): void
    {
        $defaultData = $this->defaultChannelFactory->create(localeCode: $localeCode);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given /^the store(?:| also) operates on (?:a|another) channel named "([^"]+)"$/
     * @Given /^the store(?:| also) operates on (?:a|another) channel named "([^"]+)" in "([^"]+)" currency$/
     * @Given /^the store(?:| also) operates on (?:a|another) channel named "([^"]+)" in "([^"]+)" currency and with hostname "([^"]+)"$/
     * @Given the store (also) operates on a(nother) channel named :channelName with hostname :hostname
     * @Given the store operates on a channel identified by :channelCode code
     */
    public function theStoreOperatesOnAChannelNamed(
        ?string $channelName = null,
        ?string $currencyCode = null,
        ?string $hostname = null,
        ?string $channelCode = null,
    ): void {
        $channelCode = $channelCode ?? StringInflector::nameToLowercaseCode($channelName);
        $channelName = $channelName ?? $channelCode;
        $defaultData = $this->defaultChannelFactory->create($channelCode, $channelName, $currencyCode);
        $defaultData['channel']->setHostname($hostname);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

    /**
     * @Given the channel :channel is enabled
     */
    public function theChannelIsEnabled(ChannelInterface $channel): void
    {
        $this->changeChannelState($channel, true);
    }

    /**
     * @Given the channel :channel is disabled
     * @Given the channel :channel has been disabled
     */
    public function theChannelIsDisabled(ChannelInterface $channel): void
    {
        $this->changeChannelState($channel, false);
    }

    /**
     * @Given /^the (channel "[^"]+") has showing the lowest price of discounted products (enabled|disabled)$/
     */
    public function theChannelHasShowingTheLowestPriceOfDiscountedProducts(ChannelInterface $channel, string $visible): void
    {
        $channel->getChannelPriceHistoryConfig()->setLowestPriceForDiscountedProductsVisible($visible === 'enabled');

        $this->channelManager->flush();
    }

    /**
     * @Given channel :channel has been deleted
     */
    public function iChannelHasBeenDeleted(ChannelInterface $channel): void
    {
        $this->channelRepository->remove($channel);
    }

    /**
     * @Given /^(its) default tax zone is (zone "([^"]+)")$/
     */
    public function itsDefaultTaxRateIs(ChannelInterface $channel, ZoneInterface $defaultTaxZone): void
    {
        $channel->setDefaultTaxZone($defaultTaxZone);
        $this->channelManager->flush();
    }

    /**
     * @Given /^(this channel) has contact email set as "([^"]+)"$/
     * @Given /^(this channel) has no contact email set$/
     */
    public function thisChannelHasContactEmailSetAs(ChannelInterface $channel, ?string $contactEmail = null): void
    {
        $channel->setContactEmail($contactEmail);
        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) shipping step is skipped if only a single shipping method is available$/
     */
    public function onThisChannelShippingStepIsSkippedIfOnlyASingleShippingMethodIsAvailable(ChannelInterface $channel): void
    {
        $channel->setSkippingShippingStepAllowed(true);

        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) payment step is skipped if only a single payment method is available$/
     */
    public function onThisChannelPaymentStepIsSkippedIfOnlyASinglePaymentMethodIsAvailable(
        ChannelInterface $channel,
    ): void {
        $channel->setSkippingPaymentStepAllowed(true);

        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) account verification is not required$/
     */
    public function onThisChannelAccountVerificationIsNotRequired(ChannelInterface $channel): void
    {
        $channel->setAccountVerificationRequired(false);

        $this->channelManager->flush();
    }

    /**
     * @Given /^on (this channel) account verification is required$/
     */
    public function onThisChannelAccountVerificationIsRequired(ChannelInterface $channel): void
    {
        $channel->setAccountVerificationRequired(true);

        $this->channelManager->flush();
    }

    /**
     * @Given channel :channel billing data is :company, :street, :postcode :city, :country with :taxId tax ID
     */
    public function channelBillingDataIs(
        ChannelInterface $channel,
        string $company,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country,
        string $taxId,
    ): void {
        $shopBillingData = new ShopBillingData();
        $shopBillingData->setCompany($company);
        $shopBillingData->setStreet($street);
        $shopBillingData->setPostcode($postcode);
        $shopBillingData->setCity($city);
        $shopBillingData->setCountryCode($country->getCode());
        $shopBillingData->setTaxId($taxId);

        $channel->setShopBillingData($shopBillingData);

        $this->channelManager->flush();
    }

    /**
     * @Given channel :channel has menu taxon :taxon
     * @Given /^(this channel) has menu (taxon "[^"]+")$/
     */
    public function channelHasMenuTaxon(ChannelInterface $channel, TaxonInterface $taxon): void
    {
        $channel->setMenuTaxon($taxon);

        $this->channelManager->flush();
    }

    /**
     * @Given /^(this channel) operates in the ("[^"]+" country)$/
     */
    public function channelOperatesInCountry(ChannelInterface $channel, CountryInterface $country): void
    {
        $channel->addCountry($country);

        $this->channelManager->flush();
    }

    /**
     * @Given /^(this channel) does not define operating countries$/
     */
    public function channelDoesNotDefineOperatingCountries(ChannelInterface $channel): void
    {
        foreach ($channel->getCountries() as $country) {
            $channel->removeCountry($country);
        }

        $this->channelManager->flush();
    }

    /**
     * @Given /^I changed (?:|back )my current (channel to "([^"]+)")$/
     * @When /^I change (?:|back )my current (channel to "([^"]+)")$/
     * @When customer view shop on :channel channel
     * @When I am in the :channel channel
     */
    public function iChangeMyCurrentChannelTo(ChannelInterface $channel): void
    {
        $this->sharedStorage->set('channel', $channel);
        $this->sharedStorage->set('hostname', $channel->getHostname());
        $this->channelContextSetter->setChannel($channel);
    }

    /**
     * @Given /^its required address in the checkout is (billing|shipping)$/
     */
    public function itsRequiredAddressInTheCheckoutIs(string $type): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->setShippingAddressInCheckoutRequired($type === 'shipping');

        $this->channelManager->flush();
    }

    /**
     * @Given /^(this channel) has (\d+) day(?:|s) set as the lowest price for discounted products checking period$/
     */
    public function thisChannelHasDaysSetAsTheLowestPriceForDiscountedProductsCheckingPeriod(
        ChannelInterface $channel,
        int $days,
    ): void {
        $channel->getChannelPriceHistoryConfig()->setLowestPriceForDiscountedProductsCheckingPeriod($days);

        $this->channelManager->flush();
    }

    /**
     * @Given the :taxon taxon is excluded from showing the lowest price of discounted products in the :channel channel
     */
    public function theTaxonIsExcludedFromShowingTheLowestPriceOfDiscountedProductsInTheChannel(
        TaxonInterface $taxon,
        ChannelInterface $channel,
    ): void {
        $channel->getChannelPriceHistoryConfig()->addTaxonExcludedFromShowingLowestPrice($taxon);

        $this->channelManager->flush();
    }

    /**
     * @Given /^the lowest price of discounted products prior to the current discount is disabled on (this channel)$/
     */
    public function theLowestPriceOfDiscountedProductsPriorToTheCurrentDiscountIsDisabledOnThisChannel(ChannelInterface $channel): void
    {
        $channel->getChannelPriceHistoryConfig()->setLowestPriceForDiscountedProductsVisible(false);
    }

    /**
     * @Given the store also operates in :locale locale
     */
    public function theStoreAlsoOperatesInLocale(LocaleInterface $locale): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->addLocale($locale);

        $this->channelManager->flush();
    }

    /**
     * @Given the store uses the :taxCalculationStrategy tax calculation strategy
     */
    public function theStoreUsesTheTaxCalculationStrategy(string $taxCalculationStrategy): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->setTaxCalculationStrategy(StringInflector::nameToLowercaseCode($taxCalculationStrategy));

        $this->channelManager->flush();
    }

    private function changeChannelState(ChannelInterface $channel, bool $state): void
    {
        $channel->setEnabled($state);
        $this->channelManager->flush();
        $this->sharedStorage->set('channel', $channel);
    }
}
