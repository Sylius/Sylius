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

interface ChannelInterface extends
    BaseChannelInterface,
    CurrenciesAwareInterface,
    LocalesAwareInterface
{
    /**
     * @return CurrencyInterface|null
     */
    public function getBaseCurrency(): ?CurrencyInterface;

    /**
     * @param CurrencyInterface|null $currency
     */
    public function setBaseCurrency(?CurrencyInterface $currency): void;

    /**
     * @return LocaleInterface|null
     */
    public function getDefaultLocale(): ?LocaleInterface;

    /**
     * @param LocaleInterface|null $locale
     */
    public function setDefaultLocale(?LocaleInterface $locale): void;

    /**
     * @return ZoneInterface|null
     */
    public function getDefaultTaxZone(): ?ZoneInterface;

    /**
     * @param ZoneInterface|null $defaultTaxZone
     */
    public function setDefaultTaxZone(?ZoneInterface $defaultTaxZone): void;

    /**
     * @return string|null
     */
    public function getTaxCalculationStrategy(): ?string;

    /**
     * @param string|null $taxCalculationStrategy
     */
    public function setTaxCalculationStrategy(?string $taxCalculationStrategy): void;

    /**
     * @return string|null
     */
    public function getThemeName(): ?string;

    /**
     * @param string|null $themeName
     */
    public function setThemeName(?string $themeName): void;

    /**
     * @return string|null
     */
    public function getContactEmail(): ?string;

    /**
     * @param string|null $contactEmail
     */
    public function setContactEmail(?string $contactEmail): void;

    /**
     * @return bool
     */
    public function isSkippingShippingStepAllowed(): bool;

    /**
     * @param bool $skippingShippingStepAllowed
     */
    public function setSkippingShippingStepAllowed(bool $skippingShippingStepAllowed): void;

    /**
     * @return bool
     */
    public function isSkippingPaymentStepAllowed(): bool;

    /**
     * @param bool $skippingPaymentStepAllowed
     */
    public function setSkippingPaymentStepAllowed(bool $skippingPaymentStepAllowed): void;

    /**
     * @return bool
     */
    public function isAccountVerificationRequired(): bool;

    /**
     * @param bool $accountVerificationRequired
     */
    public function setAccountVerificationRequired(bool $accountVerificationRequired): void;
}
