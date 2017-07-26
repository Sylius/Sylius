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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Currency\Model\CurrenciesAwareInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Model\LocalesAwareInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelInterface extends
    BaseChannelInterface,
    CurrenciesAwareInterface,
    LocalesAwareInterface
{
    /**
     * @return CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setBaseCurrency(CurrencyInterface $currency);

    /**
     * @return LocaleInterface
     */
    public function getDefaultLocale();

    /**
     * @param LocaleInterface $locale
     */
    public function setDefaultLocale(LocaleInterface $locale);

    /**
     * @return ZoneInterface
     */
    public function getDefaultTaxZone();

    /**
     * @param ZoneInterface $defaultTaxZone
     */
    public function setDefaultTaxZone(ZoneInterface $defaultTaxZone);

    /**
     * @return string
     */
    public function getTaxCalculationStrategy();

    /**
     * @param string $taxCalculationStrategy
     */
    public function setTaxCalculationStrategy($taxCalculationStrategy);

    /**
     * @return string
     */
    public function getThemeName();

    /**
     * @param string $themeName
     */
    public function setThemeName($themeName);

    /**
     * @return string
     */
    public function getContactEmail();

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail);

    /**
     * @return bool
     */
    public function isSkippingShippingStepAllowed();

    /**
     * @param bool $skippingShippingStepAllowed
     */
    public function setSkippingShippingStepAllowed($skippingShippingStepAllowed);

    /**
     * @return bool
     */
    public function isSkippingPaymentStepAllowed();

    /**
     * @param bool $skippingPaymentStepAllowed
     */
    public function setSkippingPaymentStepAllowed($skippingPaymentStepAllowed);

    /**
     * @return bool
     */
    public function isAccountVerificationRequired();

    /**
     * @param bool $accountVerificationRequired
     */
    public function setAccountVerificationRequired($accountVerificationRequired);
}
