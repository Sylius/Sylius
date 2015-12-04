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
use Sylius\Component\Channel\Model\Channel as BaseChannel;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface as BaseShippingMethodInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface as BaseTaxonomyInterface;

/**
 * Core channel model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Channel extends BaseChannel implements ChannelInterface
{
    /**
     * @var CurrencyInterface
     */
    protected $defaultCurrency;

    /**
     * @var LocaleInterface
     */
    protected $defaultLocale;

    /**
     * @var CurrencyInterface[]|Collection
     */
    protected $currencies;

    /**
     * @var LocaleInterface[]|Collection
     */
    protected $locales;

    /**
     * @var PaymentMethodInterface[]|Collection
     */
    protected $paymentMethods;

    /**
     * @var BaseShippingMethodInterface[]|Collection
     */
    protected $shippingMethods;

    /**
     * @var BaseTaxonomyInterface[]|Collection
     */
    protected $taxonomies;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->currencies = new ArrayCollection();
        $this->locales = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->shippingMethods = new ArrayCollection();
        $this->taxonomies = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultCurrency(CurrencyInterface $defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
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
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencies(Collection $currencies)
    {
        $this->currencies = $currencies;

        return $this;
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
    public function setLocales(Collection $locales)
    {
        $this->locales = $locales;

        return $this;
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
    public function getShippingMethods()
    {
        return $this->shippingMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function addShippingMethod(BaseShippingMethodInterface $shippingMethod)
    {
        if (!$this->hasShippingMethod($shippingMethod)) {
            $this->shippingMethods->add($shippingMethod);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeShippingMethod(BaseShippingMethodInterface $shippingMethod)
    {
        if ($this->hasShippingMethod($shippingMethod)) {
            $this->shippingMethods->removeElement($shippingMethod);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingMethod(BaseShippingMethodInterface $shippingMethod)
    {
        return $this->shippingMethods->contains($shippingMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function addPaymentMethod(PaymentMethodInterface $paymentMethod)
    {
        if (!$this->hasPaymentMethod($paymentMethod)) {
            $this->paymentMethods->add($paymentMethod);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePaymentMethod(PaymentMethodInterface $paymentMethod)
    {
        if ($this->hasPaymentMethod($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPaymentMethod(PaymentMethodInterface $paymentMethod)
    {
        return $this->paymentMethods->contains($paymentMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxonomies()
    {
        return $this->taxonomies;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxonomies(Collection $taxonomies)
    {
        $this->taxonomies = $taxonomies;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTaxonomy(BaseTaxonomyInterface $taxonomy)
    {
        if (!$this->hasTaxonomy($taxonomy)) {
            $this->taxonomies->add($taxonomy);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxonomy(BaseTaxonomyInterface $taxonomy)
    {
        if ($this->hasTaxonomy($taxonomy)) {
            $this->taxonomies->removeElement($taxonomy);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxonomy(BaseTaxonomyInterface $taxonomy)
    {
        return $this->taxonomies->contains($taxonomy);
    }
}
