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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\Channel as BaseChannel;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class ChannelTest extends TestCase
{
    private CurrencyInterface&MockObject $currency;

    private LocaleInterface&MockObject $locale;

    private Channel $channel;

    protected function setUp(): void
    {
        $this->currency = $this->createMock(CurrencyInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->channel = new Channel();
    }

    public function testShouldImplementChannelInterface(): void
    {
        $this->assertInstanceOf(ChannelInterface::class, $this->channel);
    }

    public function testShouldExtendChannel(): void
    {
        $this->assertInstanceOf(BaseChannel::class, $this->channel);
    }

    public function testShouldNotHaveBaseCurrencyByDefault(): void
    {
        $this->assertNull($this->channel->getBaseCurrency());
    }

    public function testShouldBaseCurrencyBeMutable(): void
    {
        $this->channel->setBaseCurrency($this->currency);

        $this->assertSame($this->currency, $this->channel->getBaseCurrency());
    }

    public function testShouldNotHaveDefaultLocaleByDefault(): void
    {
        $this->assertNull($this->channel->getDefaultLocale());
    }

    public function testShouldDefaultLocaleBeMutable(): void
    {
        $this->channel->setDefaultLocale($this->locale);

        $this->assertSame($this->locale, $this->channel->getDefaultLocale());
    }

    public function testShouldNotHaveDefaultTaxZoneByDefault(): void
    {
        $this->assertNull($this->channel->getDefaultTaxZone());
    }

    public function testShouldDefaultTaxZoneBeMutable(): void
    {
        $defaultTaxZone = $this->createMock(ZoneInterface::class);

        $this->channel->setDefaultTaxZone($defaultTaxZone);

        $this->assertSame($defaultTaxZone, $this->channel->getDefaultTaxZone());
    }

    public function testShouldNotHaveTaxCalculationStrategyByDefault(): void
    {
        $this->assertNull($this->channel->getTaxCalculationStrategy());
    }

    public function testShouldTaxCalculationStrategyBeMutable(): void
    {
        $this->channel->setTaxCalculationStrategy('tax_calculation_strategy');

        $this->assertSame('tax_calculation_strategy', $this->channel->getTaxCalculationStrategy());
    }

    public function testShouldHaveEmptyCollectionOfCurrenciesByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->channel->getCurrencies());
        $this->assertSame(0, $this->channel->getCurrencies()->count());
    }

    public function testShouldAddCurrency(): void
    {
        $this->channel->addCurrency($this->currency);

        $this->assertTrue($this->channel->hasCurrency($this->currency));
    }

    public function testShouldRemoveCurrency(): void
    {
        $this->channel->addCurrency($this->currency);

        $this->channel->removeCurrency($this->currency);

        $this->assertFalse($this->channel->hasCurrency($this->currency));
    }

    public function testShouldHaveEmptyCollectionOfLocalesByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->channel->getLocales());
        $this->assertSame(0, $this->channel->getLocales()->count());
    }

    public function testShouldAddLocale(): void
    {
        $this->channel->addLocale($this->locale);

        $this->assertTrue($this->channel->hasLocale($this->locale));
    }

    public function testShouldRemoveLocale(): void
    {
        $this->channel->addLocale($this->locale);

        $this->channel->removeLocale($this->locale);

        $this->assertFalse($this->channel->hasLocale($this->locale));
    }

    public function testShouldNotHaveThemeNameByDefault(): void
    {
        $this->assertNull($this->channel->getThemeName());
    }

    public function testShouldThemeNameBeMutable(): void
    {
        $this->channel->setThemeName('theme_name');

        $this->assertSame('theme_name', $this->channel->getThemeName());
    }

    public function testShouldNotHaveContactEmailByDefault(): void
    {
        $this->assertNull($this->channel->getContactEmail());
    }

    public function testShouldContactEmailBeMutablets_contact_email_is_mutable(): void
    {
        $this->channel->setContactEmail('contact@example.com');

        $this->assertSame('contact@example.com', $this->channel->getContactEmail());
    }

    public function testShouldNotHaveContactPhoneNumberByDefault(): void
    {
        $this->assertNull($this->channel->getContactPhoneNumber());
    }

    public function testShouldContactPhoneNumberBeMutable(): void
    {
        $this->channel->setContactPhoneNumber('113321122');

        $this->assertSame('113321122', $this->channel->getContactPhoneNumber());
    }

    public function testShouldAllowToSkipShippingStepIfOnlySingleShippingMethodIsResolved(): void
    {
        $this->channel->setSkippingShippingStepAllowed(true);

        $this->assertTrue($this->channel->isSkippingShippingStepAllowed());
    }

    public function testShouldAllowSkipPaymentStepIfOnlySinglePaymentMethodIsResolved(): void
    {
        $this->channel->setSkippingPaymentStepAllowed(true);

        $this->assertTrue($this->channel->isSkippingPaymentStepAllowed());
    }

    public function testShouldHaveAccountVerificationRequiredByDefault(): void
    {
        $this->assertTrue($this->channel->isAccountVerificationRequired());
    }

    public function testShouldAccountVerifciationBeMutable(): void
    {
        $this->channel->setAccountVerificationRequired(false);

        $this->assertFalse($this->channel->isAccountVerificationRequired());
    }

    public function testShouldNotHaveShippingAddressInCheckoutRequiredByDefault(): void
    {
        $this->assertFalse($this->channel->isShippingAddressInCheckoutRequired());
    }

    public function testShouldShippingAddressInCheckoutRequired(): void
    {
        $this->channel->setShippingAddressInCheckoutRequired(true);

        $this->assertTrue($this->channel->isShippingAddressInCheckoutRequired());
    }

    public function testShouldMenuTaxonBeMutable(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);

        $this->channel->setMenuTaxon($taxon);

        $this->assertSame($taxon, $this->channel->getMenuTaxon());
    }

    public function testShouldPriceHistoryConfigBeMutable(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);

        $this->channel->setChannelPriceHistoryConfig($config);

        $this->assertSame($config, $this->channel->getChannelPriceHistoryConfig());
    }

    public function testShouldReturnOnlyEnabledCountries(): void
    {
        $country1 = $this->createMock(CountryInterface::class);
        $country2 = $this->createMock(CountryInterface::class);

        $country1->method('isEnabled')->willReturn(true);
        $country2->method('isEnabled')->willReturn(false);

        $this->channel->addCountry($country1);
        $this->channel->addCountry($country2);

        $enabledCountries = $this->channel->getEnabledCountries();

        $this->assertCount(1, $enabledCountries);
        $this->assertContains($country1, $enabledCountries);
        $this->assertNotContains($country2, $enabledCountries);
    }
}
