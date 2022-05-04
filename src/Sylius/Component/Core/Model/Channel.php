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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\Channel as BaseChannel;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class Channel extends BaseChannel implements ChannelInterface
{
    /** @var CurrencyInterface|null */
    protected $baseCurrency;

    /** @var LocaleInterface|null */
    protected $defaultLocale;

    /** @var ZoneInterface|null */
    protected $defaultTaxZone;

    /** @var string|null */
    protected $taxCalculationStrategy;

    /**
     * @var Collection|CurrencyInterface[]
     *
     * @psalm-var Collection<array-key, CurrencyInterface>
     */
    protected $currencies;

    /**
     * @var Collection|LocaleInterface[]
     *
     * @psalm-var Collection<array-key, LocaleInterface>
     */
    protected $locales;

    /**
     * @var Collection|CountryInterface[]
     *
     * @psalm-var Collection<array-key, CountryInterface>
     */
    protected $countries;

    /** @var string|null */
    protected $themeName;

    /** @var string|null */
    protected $contactEmail;

    /** @var string|null */
    protected $contactPhoneNumber;

    /** @var bool */
    protected $skippingShippingStepAllowed = false;

    /** @var bool */
    protected $skippingPaymentStepAllowed = false;

    /** @var bool */
    protected $accountVerificationRequired = true;

    /** @var ShopBillingDataInterface|null */
    protected $shopBillingData;

    /** @var TaxonInterface|null */
    protected $menuTaxon;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, CurrencyInterface> $this->currencies */
        $this->currencies = new ArrayCollection();
        /** @var ArrayCollection<array-key, LocaleInterface> $this->locales */
        $this->locales = new ArrayCollection();
        /** @var ArrayCollection<array-key, CountryInterface> $this->countries */
        $this->countries = new ArrayCollection();
    }

    public function getBaseCurrency(): ?CurrencyInterface
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(?CurrencyInterface $currency): void
    {
        $this->baseCurrency = $currency;
    }

    public function getDefaultLocale(): ?LocaleInterface
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(?LocaleInterface $locale): void
    {
        $this->defaultLocale = $locale;
    }

    public function getDefaultTaxZone(): ?ZoneInterface
    {
        return $this->defaultTaxZone;
    }

    public function setDefaultTaxZone(?ZoneInterface $defaultTaxZone): void
    {
        $this->defaultTaxZone = $defaultTaxZone;
    }

    public function getTaxCalculationStrategy(): ?string
    {
        return $this->taxCalculationStrategy;
    }

    public function setTaxCalculationStrategy(?string $taxCalculationStrategy): void
    {
        $this->taxCalculationStrategy = $taxCalculationStrategy;
    }

    public function getCurrencies(): Collection
    {
        return $this->currencies;
    }

    public function addCurrency(CurrencyInterface $currency): void
    {
        if (!$this->hasCurrency($currency)) {
            $this->currencies->add($currency);
        }
    }

    public function removeCurrency(CurrencyInterface $currency): void
    {
        if ($this->hasCurrency($currency)) {
            $this->currencies->removeElement($currency);
        }
    }

    public function hasCurrency(CurrencyInterface $currency): bool
    {
        return $this->currencies->contains($currency);
    }

    public function getLocales(): Collection
    {
        return $this->locales;
    }

    public function addLocale(LocaleInterface $locale): void
    {
        if (!$this->hasLocale($locale)) {
            $this->locales->add($locale);
        }
    }

    public function removeLocale(LocaleInterface $locale): void
    {
        if ($this->hasLocale($locale)) {
            $this->locales->removeElement($locale);
        }
    }

    public function hasLocale(LocaleInterface $locale): bool
    {
        return $this->locales->contains($locale);
    }

    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(CountryInterface $country): void
    {
        if (!$this->hasCountry($country)) {
            $this->countries->add($country);
        }
    }

    public function removeCountry(CountryInterface $country): void
    {
        if ($this->hasCountry($country)) {
            $this->countries->removeElement($country);
        }
    }

    public function hasCountry(CountryInterface $country): bool
    {
        return $this->countries->contains($country);
    }

    public function getThemeName(): ?string
    {
        return $this->themeName;
    }

    public function setThemeName(?string $themeName): void
    {
        $this->themeName = $themeName;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    public function getContactPhoneNumber(): ?string
    {
        return $this->contactPhoneNumber;
    }

    public function setContactPhoneNumber(?string $contactPhoneNumber): void
    {
        $this->contactPhoneNumber = $contactPhoneNumber;
    }

    public function isSkippingShippingStepAllowed(): bool
    {
        return $this->skippingShippingStepAllowed;
    }

    public function setSkippingShippingStepAllowed(bool $skippingShippingStepAllowed): void
    {
        $this->skippingShippingStepAllowed = $skippingShippingStepAllowed;
    }

    public function isSkippingPaymentStepAllowed(): bool
    {
        return $this->skippingPaymentStepAllowed;
    }

    public function setSkippingPaymentStepAllowed(bool $skippingPaymentStepAllowed): void
    {
        $this->skippingPaymentStepAllowed = $skippingPaymentStepAllowed;
    }

    public function isAccountVerificationRequired(): bool
    {
        return $this->accountVerificationRequired;
    }

    public function setAccountVerificationRequired(bool $accountVerificationRequired): void
    {
        $this->accountVerificationRequired = $accountVerificationRequired;
    }

    public function getShopBillingData(): ?ShopBillingDataInterface
    {
        return $this->shopBillingData;
    }

    public function setShopBillingData(ShopBillingDataInterface $shopBillingData): void
    {
        $this->shopBillingData = $shopBillingData;
    }

    public function getMenuTaxon(): ?TaxonInterface
    {
        return $this->menuTaxon;
    }

    public function setMenuTaxon(?TaxonInterface $menuTaxon): void
    {
        $this->menuTaxon = $menuTaxon;
    }
}
