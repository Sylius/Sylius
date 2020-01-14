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

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\Channel as BaseChannel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class ChannelSpec extends ObjectBehavior
{
    function it_implements_a_channel_interface(): void
    {
        $this->shouldImplement(ChannelInterface::class);
    }

    function it_extends_a_channel(): void
    {
        $this->shouldHaveType(BaseChannel::class);
    }

    function it_has_no_base_currency_by_default(): void
    {
        $this->getBaseCurrency()->shouldReturn(null);
    }

    function its_base_currency_is_mutable(CurrencyInterface $baseCurrency): void
    {
        $this->setBaseCurrency($baseCurrency);
        $this->getBaseCurrency()->shouldReturn($baseCurrency);
    }

    function it_has_no_default_locale_by_default(): void
    {
        $this->getDefaultLocale()->shouldReturn(null);
    }

    function its_default_locale_is_mutable(LocaleInterface $defaultLocale): void
    {
        $this->setDefaultLocale($defaultLocale);
        $this->getDefaultLocale()->shouldReturn($defaultLocale);
    }

    function it_has_no_default_tax_zone_by_default(): void
    {
        $this->getDefaultTaxZone()->shouldReturn(null);
    }

    function its_default_tax_zone_is_mutable(ZoneInterface $defaultTaxZone): void
    {
        $this->setDefaultTaxZone($defaultTaxZone);
        $this->getDefaultTaxZone()->shouldReturn($defaultTaxZone);
    }

    function it_has_no_tax_calculation_strategy_by_default(): void
    {
        $this->getTaxCalculationStrategy()->shouldReturn(null);
    }

    function its_tax_calculation_strategy_is_mutable(): void
    {
        $this->setTaxCalculationStrategy('tax_calculation_strategy');
        $this->getTaxCalculationStrategy()->shouldReturn('tax_calculation_strategy');
    }

    function it_has_an_empty_collection_of_currencies_by_default(): void
    {
        $this->getCurrencies()->shouldHaveType(Collection::class);
        $this->getCurrencies()->count()->shouldReturn(0);
    }

    function it_can_have_a_currency_added(CurrencyInterface $currency): void
    {
        $this->addCurrency($currency);
        $this->hasCurrency($currency)->shouldReturn(true);
    }

    function it_can_have_a_currency_removed(CurrencyInterface $currency): void
    {
        $this->addCurrency($currency);
        $this->removeCurrency($currency);
        $this->hasCurrency($currency)->shouldReturn(false);
    }

    function it_has_an_empty_collection_of_locales_by_default(): void
    {
        $this->getLocales()->shouldHaveType(Collection::class);
        $this->getLocales()->count()->shouldReturn(0);
    }

    function it_can_have_a_locale_added(LocaleInterface $locale): void
    {
        $this->addLocale($locale);
        $this->hasLocale($locale)->shouldReturn(true);
    }

    function it_can_have_a_locale_removed(LocaleInterface $locale): void
    {
        $this->addLocale($locale);
        $this->removeLocale($locale);
        $this->hasLocale($locale)->shouldReturn(false);
    }

    function it_has_no_theme_name_by_default(): void
    {
        $this->getThemeName()->shouldReturn(null);
    }

    function its_theme_name_is_mutable(): void
    {
        $this->setThemeName('theme_name');
        $this->getThemeName()->shouldReturn('theme_name');
    }

    function it_has_no_contact_email_by_default(): void
    {
        $this->getContactEmail()->shouldReturn(null);
    }

    function its_contact_email_is_mutable(): void
    {
        $this->setContactEmail('contact@example.com');
        $this->getContactEmail()->shouldReturn('contact@example.com');
    }

    function it_can_allow_to_skip_shipping_step_if_only_a_single_shipping_method_is_resolved(): void
    {
        $this->setSkippingShippingStepAllowed(true);
        $this->isSkippingShippingStepAllowed()->shouldReturn(true);
    }

    function it_can_allow_to_skip_payment_step_if_only_a_single_payment_method_is_resolved(): void
    {
        $this->setSkippingPaymentStepAllowed(true);
        $this->isSkippingPaymentStepAllowed()->shouldReturn(true);
    }

    function it_has_account_verification_required_by_default(): void
    {
        $this->isAccountVerificationRequired()->shouldReturn(true);
    }

    function it_can_set_account_verification_required(): void
    {
        $this->setAccountVerificationRequired(false);
        $this->isAccountVerificationRequired()->shouldReturn(false);
    }

    function its_menu_taxon_is_mutable(TaxonInterface $taxon): void
    {
        $this->setMenuTaxon($taxon);
        $this->getMenuTaxon()->shouldReturn($taxon);
    }
}
