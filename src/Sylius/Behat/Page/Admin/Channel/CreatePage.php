<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Channel;

use Sylius\Behat\Behaviour\DescribesIt;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;
    use DescribesIt;
    use Toggles;

    /**
     * {@inheritdoc}
     */
    public function setHostname($hostname)
    {
        $this->getDocument()->fillField('Hostname', $hostname);
    }

    /**
     * {@inheritdoc}
     */
    public function setContactEmail($contactEmail)
    {
        $this->getDocument()->fillField('Contact email', $contactEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function defineColor($color)
    {
        $this->getDocument()->fillField('Color', $color);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseCurrency($currencyCode)
    {
        $this->getDocument()->selectFieldOption('Currencies', $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseLocale($language)
    {
        $this->getDocument()->selectFieldOption('Locales', $language);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseDefaultTaxZone($taxZone)
    {
        $this->getDocument()->selectFieldOption('Default tax zone', $taxZone);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseDefaultLocale($locale)
    {
        if (null !== $locale) {
            $this->getElement('locales')->selectOption($locale);
            $this->getElement('default_locale')->selectOption($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function chooseBaseCurrency($currency)
    {
        if (null !== $currency) {
            $this->getElement('currencies')->selectOption($currency);
            $this->getElement('base_currency')->selectOption($currency);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function chooseTaxCalculationStrategy($taxZone)
    {
        $this->getDocument()->selectFieldOption('Tax calculation strategy', $taxZone);
    }

    public function allowToSkipShippingStep()
    {
        $this->getDocument()->checkField('Skip shipping step if only one shipping method is available?');
    }

    public function allowToSkipPaymentStep()
    {
        $this->getDocument()->checkField('Skip payment step if only one payment method is available?');
    }

    /**
     * {@inheritdoc}
     */
    protected function getToggleableElement()
    {
        return $this->getElement('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_channel_code',
            'currencies' => '#sylius_channel_currencies',
            'base_currency' => '#sylius_channel_baseCurrency',
            'default_locale' => '#sylius_channel_defaultLocale',
            'enabled' => '#sylius_channel_enabled',
            'locales' => '#sylius_channel_locales',
            'name' => '#sylius_channel_name',
        ]);
    }
}
