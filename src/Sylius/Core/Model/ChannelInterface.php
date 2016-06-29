<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\ThemeBundle\Model\ThemeInterface;
use Sylius\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Currency\Model\CurrenciesAwareInterface;
use Sylius\Currency\Model\CurrencyInterface;
use Sylius\Locale\Model\LocaleInterface;
use Sylius\Locale\Model\LocalesAwareInterface;
use Sylius\Payment\Model\PaymentMethodsAwareInterface;
use Sylius\Shipping\Model\ShippingMethodsAwareInterface;
use Sylius\Taxonomy\Model\TaxonsAwareInterface;

/**
 * Model implementing this interface should reference several:
 *   - Currencies;
 *   - Locales;
 *   - Payment methods;
 *   - Shipping methods;
 *   - Taxons.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelInterface extends
    BaseChannelInterface,
    CurrenciesAwareInterface,
    LocalesAwareInterface,
    PaymentMethodsAwareInterface,
    ShippingMethodsAwareInterface,
    TaxonsAwareInterface
{
    /**
     * @return string
     */
    public function getThemeName();

    /**
     * @param string $themeName
     */
    public function setThemeName($themeName);

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
