<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use Toggles;
    
    /**
     * {@inheritdoc}
     */
    public function changeExchangeRate($exchangeRate)
    {
        $this->getDocument()->fillField('Exchange rate', $exchangeRate);
    }

    /**
     * {@inheritdoc}
     */
    public function getExchangeRateValue()
    {
        return $this->getElement('exchangeRate')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeDisabledAttribute()
    {
        return $this->getElement('code')->getAttribute('disabled');
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
            'enabled' => '#sylius_currency_enabled',
            'exchangeRate' => '#sylius_currency_exchangeRate',
            'code' => '#sylius_currency_code',
        ]);
    }
}
