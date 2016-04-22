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
    public function chooseCurrency($currency)
    {
        $this->getDocument()->selectFieldOption('Currencies', $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrencyChosen($currency)
    {
        return $this->getElement('currencies')->find('named', array('option', $currency))->hasAttribute('selected');
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
    public function isPaymentMethodChosen($paymentMethod)
    {
        return $this->getElement('payment_methods')->find('named', array('option', $paymentMethod))->hasAttribute('selected');
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
            'enabled' => '#sylius_channel_enabled',
            'locales' => '#sylius_channel_locales',
            'currencies' => '#sylius_channel_currencies',
            'shipping_methods' => '#sylius_channel_shippingMethods',
            'payment_methods' => '#sylius_channel_paymentMethods',
            'name' => '#sylius_channel_name',
        ]);
    }
}
