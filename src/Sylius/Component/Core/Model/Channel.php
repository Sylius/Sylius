<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\Channel as BaseChannel;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Channel extends BaseChannel implements ChannelInterface
{
    /**
     * @var CurrencyInterface
     */
    protected $baseCurrency;

    /**
     * @var LocaleInterface
     */
    protected $defaultLocale;

    /**
     * @var ZoneInterface
     */
    protected $defaultTaxZone;

    /**
     * @var string
     */
    protected $taxCalculationStrategy;

    /**
     * @var CurrencyInterface[]|Collection
     */
    protected $currencies;

    /**
     * @var LocaleInterface[]|Collection
     */
    protected $locales;

    /**
     * @var string
     */
    protected $themeName;

    /**
     * @var string
     */
    protected $contactEmail;

    /**
     * @var bool
     */
    protected $skippingShippingStepAllowed = false;

    /**
     * @var bool
     */
    protected $accountVerificationRequired = true;

    public function __construct()
    {
        parent::__construct();

        $this->currencies = new ArrayCollection();
        $this->locales = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseCurrency(CurrencyInterface $baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultLocale(LocaleInterface $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTaxZone()
    {
        return $this->defaultTaxZone;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultTaxZone(ZoneInterface $defaultTaxZone = null)
    {
        $this->defaultTaxZone = $defaultTaxZone;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculationStrategy()
    {
        return $this->taxCalculationStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCalculationStrategy($taxCalculationStrategy)
    {
        $this->taxCalculationStrategy = $taxCalculationStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function addCurrency(CurrencyInterface $currency)
    {
        if (!$this->hasCurrency($currency)) {
            $this->currencies->add($currency);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCurrency(CurrencyInterface $currency)
    {
        if ($this->hasCurrency($currency)) {
            $this->currencies->removeElement($currency);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCurrency(CurrencyInterface $currency)
    {
        return $this->currencies->contains($currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * {@inheritdoc}
     */
    public function addLocale(LocaleInterface $locale)
    {
        if (!$this->hasLocale($locale)) {
            $this->locales->add($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeLocale(LocaleInterface $locale)
    {
        if ($this->hasLocale($locale)) {
            $this->locales->removeElement($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasLocale(LocaleInterface $locale)
    {
        return $this->locales->contains($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * {@inheritdoc}
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function isSkippingShippingStepAllowed()
    {
        return $this->skippingShippingStepAllowed;
    }

    /**
     * {@inheritdoc}
     */
    public function setSkippingShippingStepAllowed($skippingShippingStepAllowed)
    {
        $this->skippingShippingStepAllowed = $skippingShippingStepAllowed;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountVerificationRequired()
    {
        return $this->accountVerificationRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountVerificationRequired($accountVerificationRequired)
    {
        $this->accountVerificationRequired = $accountVerificationRequired;
    }
}
