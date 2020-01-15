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

namespace Sylius\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\AutocompleteHelper;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

    public function setTheme(string $themeName): void
    {
        $this->getDocument()->selectFieldOption('Theme', $themeName);
    }

    public function unsetTheme(): void
    {
        $this->getDocument()->selectFieldOption('Theme', '');
    }

    public function chooseLocale(string $language): void
    {
        $this->getDocument()->selectFieldOption('Locales', $language);
    }

    public function chooseCurrency(string $currencyCode): void
    {
        $this->getDocument()->selectFieldOption('Currencies', $currencyCode);
    }

    public function chooseDefaultTaxZone(string $taxZone): void
    {
        $this->getDocument()->selectFieldOption('Default tax zone', $taxZone);
    }

    public function chooseTaxCalculationStrategy(string $taxZone): void
    {
        $this->getDocument()->selectFieldOption('Tax calculation strategy', $taxZone);
    }

    public function isLocaleChosen(string $language): bool
    {
        return $this->getElement('locales')->find('named', ['option', $language])->hasAttribute('selected');
    }

    public function isCurrencyChosen(string $currencyCode): bool
    {
        return $this->getElement('currencies')->find('named', ['option', $currencyCode])->hasAttribute('selected');
    }

    public function isDefaultTaxZoneChosen(string $taxZone): bool
    {
        return $this->getElement('default_tax_zone')->find('named', ['option', $taxZone])->hasAttribute('selected');
    }

    public function isAnyDefaultTaxZoneChosen(): bool
    {
        return null !== $this->getElement('default_tax_zone')->find('css', '[selected]');
    }

    public function isTaxCalculationStrategyChosen(string $taxCalculationStrategy): bool
    {
        return $this
            ->getElement('tax_calculation_strategy')
            ->find('named', ['option', $taxCalculationStrategy])
            ->hasAttribute('selected')
        ;
    }

    public function isBaseCurrencyDisabled(): bool
    {
        return $this->getElement('base_currency')->hasAttribute('disabled');
    }

    public function changeType(string $type): void
    {
        $this->getElement('type')->selectOption($type);
    }

    public function getType(): string
    {
        return $this->getElement('type')->getValue();
    }

    public function changeMenuTaxon(string $menuTaxon): void
    {
        $menuTaxonElement = $this->getElement('menu_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $menuTaxonElement, $menuTaxon);
    }

    public function getMenuTaxon(): string
    {
        return $this->getElement('menu_taxon')->getParent()->find('css', '.search > .text')->getText();
    }

    public function getUsedTheme(): string
    {
        return $this->getElement('theme')->getValue();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'base_currency' => '#sylius_channel_baseCurrency',
            'code' => '#sylius_channel_code',
            'currencies' => '#sylius_channel_currencies',
            'default_locale' => '#sylius_channel_defaultLocale',
            'default_tax_zone' => '#sylius_channel_defaultTaxZone',
            'enabled' => '#sylius_channel_enabled',
            'locales' => '#sylius_channel_locales',
            'menu_taxon' => '#sylius_channel_menuTaxon',
            'name' => '#sylius_channel_name',
            'tax_calculation_strategy' => '#sylius_channel_taxCalculationStrategy',
            'theme' => '#sylius_channel_themeName',
            'type' => '#sylius_channel_type',
        ]);
    }
}
