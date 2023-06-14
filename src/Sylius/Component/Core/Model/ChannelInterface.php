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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Currency\Model\CurrenciesAwareInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Model\LocalesAwareInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface ChannelInterface extends
    BaseChannelInterface,
    CurrenciesAwareInterface,
    LocalesAwareInterface
{
    public function getBaseCurrency(): ?CurrencyInterface;

    public function setBaseCurrency(?CurrencyInterface $currency): void;

    public function getDefaultLocale(): ?LocaleInterface;

    public function setDefaultLocale(?LocaleInterface $locale): void;

    public function getDefaultTaxZone(): ?ZoneInterface;

    public function setDefaultTaxZone(?ZoneInterface $defaultTaxZone): void;

    public function getTaxCalculationStrategy(): ?string;

    public function setTaxCalculationStrategy(?string $taxCalculationStrategy): void;

    public function getThemeName(): ?string;

    public function setThemeName(?string $themeName): void;

    public function getContactEmail(): ?string;

    public function setContactEmail(?string $contactEmail): void;

    public function getContactPhoneNumber(): ?string;

    public function setContactPhoneNumber(?string $contactPhoneNumber): void;

    public function isSkippingShippingStepAllowed(): bool;

    public function setSkippingShippingStepAllowed(bool $skippingShippingStepAllowed): void;

    public function isSkippingPaymentStepAllowed(): bool;

    public function setSkippingPaymentStepAllowed(bool $skippingPaymentStepAllowed): void;

    public function isAccountVerificationRequired(): bool;

    public function setAccountVerificationRequired(bool $accountVerificationRequired): void;

    public function isShippingAddressInCheckoutRequired(): bool;

    public function setShippingAddressInCheckoutRequired(bool $shippingAddressInCheckoutRequired): void;

    public function getShopBillingData(): ?ShopBillingDataInterface;

    public function setShopBillingData(ShopBillingDataInterface $shopBillingData): void;

    public function getMenuTaxon(): ?TaxonInterface;

    public function setMenuTaxon(?TaxonInterface $menuTaxon): void;

    /**
     * @return Collection|CountryInterface[]
     *
     * @psalm-return Collection<array-key, CountryInterface>
     */
    public function getCountries(): Collection;

    public function addCountry(CountryInterface $country): void;

    public function removeCountry(CountryInterface $country): void;

    public function hasCountry(CountryInterface $country): bool;

    public function setChannelPriceHistoryConfig(ChannelPriceHistoryConfigInterface $channelPriceHistoryConfig): void;

    public function getChannelPriceHistoryConfig(): ?ChannelPriceHistoryConfigInterface;
}
