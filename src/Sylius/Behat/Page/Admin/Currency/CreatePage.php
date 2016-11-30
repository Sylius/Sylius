<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Behaviour\ChoosesName;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChoosesName;

    /**
     * {@inheritdoc}
     */
    public function specifyExchangeRate($exchangeRate)
    {
        $this->getDocument()->fillField('Exchange rate', $exchangeRate);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_currency_code',
            'exchangeRate' => '#sylius_currency_exchangeRate',
        ]);
    }
}
