<?php

/*
 * This file is a part of the Sylius package.
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
    public function chooseShippingMethod($shippingMethod)
    {
        $this->getDocument()->selectFieldOption('Shipping Methods', $shippingMethod);
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
        $this->getDocument()->selectFieldOption('Default tax zone', $taxZone);
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
            'name' => '#sylius_channel_name',
        ]);
    }
}
