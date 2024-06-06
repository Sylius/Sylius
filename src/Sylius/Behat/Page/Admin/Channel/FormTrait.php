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

trait FormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'base_currency' => '[data-test-base-currency]',
            'code' => '[data-test-code]',
            'color' => '[data-test-color]',
            'contact_email' => '[data-test-contact-email]',
            'contact_phone_number' => '[data-test-contact-phone-number]',
            'countries' => '#sylius_admin_channel_countries',
            'currencies' => '#sylius_admin_channel_currencies',
            'default_locale' => '#sylius_admin_channel_defaultLocale',
            'default_tax_zone' => '[data-test-default-tax-zone]',
            'discounted_products_checking_period' => '[data-test-lowest-price-for-discounted-products-checking-period]',
            'enabled' => '[data-test-enabled]',
            'form' => 'form',
            'hostname' => '[data-test-hostname]',
            'locales' => '[data-test-locales]',
            'menu_taxon' => '[data-test-menu-taxon]',
            'name' => '[data-test-name]',
            'tax_calculation_strategy' => '[data-test-tax-calculation-strategy]',
            'theme' => '[data-test-theme]',
        ];
    }

    public function setHostname(string $hostname): void
    {
        $this->getElement('hostname')->setValue($hostname);
    }

    public function setTheme(string $themeName): void
    {
        $this->getElement('theme')->selectOption($themeName);
    }

    public function getTheme(): string
    {
        return $this->getElement('theme')->getValue();
    }

    public function setContactEmail(string $contactEmail): void
    {
        $this->getElement('contact_email')->setValue($contactEmail);
    }

    public function setContactPhoneNumber(string $contactPhoneNumber): void
    {
        $this->getElement('contact_phone_number')->setValue($contactPhoneNumber);
    }

    public function defineColor(string $color): void
    {
        $this->getElement('color')->setValue($color);
    }

    public function chooseCurrency(string $currencyName): void
    {
        $this->getElement('currencies')->selectOption($currencyName, true);
    }

    public function chooseLocale(string $language): void
    {
        $this->getElement('locales')->selectOption($language);
    }

    public function chooseDefaultTaxZone(string $taxZone): void
    {
        $this->getElement('default_tax_zone')->selectOption($taxZone);
    }

    public function chooseDefaultLocale(string $locale): void
    {
        $this->getElement('default_locale')->selectOption($locale);
    }

    public function chooseOperatingCountries(array $countries): void
    {
        foreach ($countries as $country) {
            $this->getElement('countries')->selectOption($country, true);
        }
    }

    public function chooseBaseCurrency(string $currency): void
    {
        $this->getElement('currencies')->selectOption($currency, true);
        $this->getElement('base_currency')->selectOption($currency);
    }

    public function getMenuTaxon(): string
    {
        return $this->getSelectedOptionText('menu_taxon');
    }

    public function specifyMenuTaxon(string $menuTaxon): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('menu_taxon')->getXpath(),
            $menuTaxon,
        );
        $this->waitForFormUpdate();
    }

    public function getTaxCalculationStrategy(): string
    {
        return $this->getSelectedOptionText('tax_calculation_strategy');
    }

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void
    {
        $this->getElement('tax_calculation_strategy')->selectOption($taxCalculationStrategy);
    }

    private function getSelectedOptionText(string $element): string
    {
        return $this
            ->getElement($element)
            ->find('css', 'option:selected')
            ->getText()
        ;
    }
}
