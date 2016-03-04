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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Currency\Model\CurrenciesAwareInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Model\LocalesAwareInterface;
use Sylius\Component\Payment\Model\PaymentMethodsAwareInterface;
use Sylius\Component\Shipping\Model\ShippingMethodsAwareInterface;
use Sylius\Component\Taxonomy\Model\TaxonomiesAwareInterface;

/**
 * Model implementing this interface should reference several:
 *   - Currencies;
 *   - Locales;
 *   - Payment methods;
 *   - Shipping methods;
 *   - Taxonomies.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ChannelInterface extends
    BaseChannelInterface,
    CurrenciesAwareInterface,
    LocalesAwareInterface,
    PaymentMethodsAwareInterface,
    ShippingMethodsAwareInterface,
    TaxonomiesAwareInterface
{
    /**
     * @return ThemeInterface
     */
    public function getTheme();

    /**
     * @param ThemeInterface|null $theme
     */
    public function setTheme(ThemeInterface $theme = null);

    /**
     * @param LocaleInterface $locale
     */
    public function setDefaultLocale(LocaleInterface $locale);

    /**
     * @return LocaleInterface
     */
    public function getDefaultLocale();

    /**
     * @param CurrencyInterface $currency
     */
    public function setDefaultCurrency(CurrencyInterface $currency);

    /**
     * @return CurrencyInterface
     */
    public function getDefaultCurrency();
}
