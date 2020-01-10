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

    public function setType(string $type): void
    {
        $this->getElement('type')->selectOption($type);
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
            'base_currency' => '#sylius_channel_baseCurrency',
            'code' => '#sylius_channel_code',
            'currencies' => '#sylius_channel_currencies',
            'default_locale' => '#sylius_channel_defaultLocale',
            'enabled' => '#sylius_channel_enabled',
            'locales' => '#sylius_channel_locales',
            'menu_taxon' => '#sylius_channel_menuTaxon',
            'name' => '#sylius_channel_name',
            'type' => '#sylius_channel_type',
        ]);
    }
}
