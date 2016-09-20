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

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

    /**
     * {@inheritdoc}
     */
    public function setTheme($themeName)
    {
        $this->getDocument()->selectFieldOption('Theme', $themeName);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetTheme()
    {
        $this->getDocument()->selectFieldOption('Theme', '');
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
    public function isLocaleChosen($language)
    {
        return $this->getElement('locales')->find('named', array('option', $language))->hasAttribute('selected');
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
    public function isCurrencyChosen($currencyCode)
    {
        return $this->getElement('currencies')->find('named', array('option', $currencyCode))->hasAttribute('selected');
    }

    /**
     * {@inheritdoc}
     */
    public function chooseShippingMethod($shippingMethod)
    {
        $this->getDocument()->selectFieldOption('Shipping Methods', $shippingMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingMethodChosen($shippingMethod)
    {
        return $this->getElement('shipping_methods')->find('named', array('option', $shippingMethod))->hasAttribute('selected');
    }

    /**
     * {@inheritdoc}
     */
    public function choosePaymentMethod($paymentMethod)
    {
        $this->getDocument()->selectFieldOption('Payment Methods', $paymentMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseDefaultTaxZone($taxZone)
    {
        $this->getDocument()->selectFieldOption('Default tax zone', (null === $taxZone) ? '' : $taxZone);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseTaxCalculationStrategy($taxZone)
    {
        $this->getDocument()->selectFieldOption('Tax calculation strategy', $taxZone);
    }

    /**
     * {@inheritdoc}
     */
    public function isPaymentMethodChosen($paymentMethod)
    {
        return $this->getElement('payment_methods')->find('named', array('option', $paymentMethod))->hasAttribute('selected');
    }

    /**
     * {@inheritdoc}
     */
    public function isDefaultTaxZoneChosen($taxZone)
    {
        return $this->getElement('default_tax_zone')->find('named', array('option', $taxZone))->hasAttribute('selected');
    }

    /**
     * {@inheritdoc}
     */
    public function isAnyDefaultTaxZoneChosen()
    {
        return null !== $this->getElement('default_tax_zone')->find('css', '[selected]');
    }

    /**
     * {@inheritdoc}
     */
    public function isTaxCalculationStrategyChosen($taxCalculationStrategy)
    {
        return $this
            ->getElement('tax_calculation_strategy')
            ->find('named', array('option', $taxCalculationStrategy))
            ->hasAttribute('selected')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
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
            'default_tax_zone' => '#sylius_channel_defaultTaxZone',
            'enabled' => '#sylius_channel_enabled',
            'locales' => '#sylius_channel_locales',
            'name' => '#sylius_channel_name',
            'payment_methods' => '#sylius_channel_paymentMethods',
            'shipping_methods' => '#sylius_channel_shippingMethods',
            'tax_calculation_strategy' => '#sylius_channel_taxCalculationStrategy',
        ]);
    }
}
