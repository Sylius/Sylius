<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ExchangeRate;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function specifyRatio($ratio)
    {
        $this->getDocument()->fillField('Ratio', $ratio);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseSourceCurrency($currency)
    {
        $this->getDocument()->selectFieldOption('Source currency', $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseTargetCurrency($currency)
    {
        $this->getDocument()->selectFieldOption('Target currency', $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFormValidationError($expectedMessage)
    {
        $formValidationErrors = $this->getDocument()->find('css', 'form > div.ui.red.label.sylius-validation-error');
        if (null === $formValidationErrors) {
            return false;
        }

        return $expectedMessage === $formValidationErrors->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'source currency' => '#sylius_exchange_rate_sourceCurrency',
            'target currency' => '#sylius_exchange_rate_targetCurrency',
            'ratio' => '#sylius_exchange_rate_ratio',
        ]);
    }
}
