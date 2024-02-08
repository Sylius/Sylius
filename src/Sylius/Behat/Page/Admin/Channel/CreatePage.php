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

namespace Sylius\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\DescribesIt;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;
    use DescribesIt;
    use Toggles;

    public function setHostname(string $hostname): void
    {
        $this->getDocument()->fillField('Hostname', $hostname);
    }

    public function setContactEmail(string $contactEmail): void
    {
        $this->getDocument()->fillField('Contact email', $contactEmail);
    }

    public function setContactPhoneNumber(string $contactPhoneNumber): void
    {
        $this->getDocument()->fillField('Contact phone number', $contactPhoneNumber);
    }

    public function defineColor(string $color): void
    {
        $this->getDocument()->fillField('Color', $color);
    }

    public function chooseCurrency(string $currencyCode): void
    {
        $this->getDocument()->selectFieldOption('Currencies', $currencyCode);
    }

    public function chooseLocale(string $language): void
    {
        $this->getDocument()->selectFieldOption('Locales', $language);
    }

    public function chooseDefaultTaxZone(string $taxZone): void
    {
        $this->getDocument()->selectFieldOption('Default tax zone', $taxZone);
    }

    public function chooseDefaultLocale(?string $locale): void
    {
        if (null !== $locale) {
            $this->getElement('default_locale')->selectOption($locale);
        }
    }

    public function chooseOperatingCountries(array $countries): void
    {
        foreach ($countries as $country) {
            $this->getElement('countries')->selectOption($country, true);
        }
    }

    public function chooseBaseCurrency(?string $currency): void
    {
        if (null !== $currency) {
            $this->getElement('currencies')->selectOption($currency);
            $this->getElement('base_currency')->selectOption($currency);
        }
    }

    public function chooseTaxCalculationStrategy(string $taxZone): void
    {
        $this->getDocument()->selectFieldOption('Tax calculation strategy', $taxZone);
    }

    public function allowToSkipShippingStep(): void
    {
        $this->getDocument()->checkField('Skip shipping step if only one shipping method is available?');
    }

    public function allowToSkipPaymentStep(): void
    {
        $this->getDocument()->checkField('Skip payment step if only one payment method is available?');
    }

    public function specifyMenuTaxon(string $menuTaxon): void
    {
        $menuTaxonElement = $this->getElement('menu_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $menuTaxonElement, $menuTaxon);
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_channel_code',
            'countries' => '#sylius_channel_countries',
            'currencies' => '#sylius_channel_currencies',
            'base_currency' => '#sylius_channel_baseCurrency',
            'default_locale' => '#sylius_channel_defaultLocale',
            'enabled' => '#sylius_channel_enabled',
            'locales' => '#sylius_channel_locales',
            'menu_taxon' => '#sylius_channel_menuTaxon',
            'name' => '#sylius_channel_name',
        ]);
    }
}
