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
    public function chooseBaseCurrency($currency)
    {
        $this->getDocument()->selectFieldOption('Base currency', $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseCounterCurrency($currency)
    {
        $this->getDocument()->selectFieldOption('Counter currency', $currency);
    }
}
