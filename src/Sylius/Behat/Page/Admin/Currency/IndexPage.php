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

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCurrencyDisabled(CurrencyInterface $currency)
    {
        return $this->checkCurrencyStatus($currency, 'Disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrencyEnabled(CurrencyInterface $currency)
    {
        return $this->checkCurrencyStatus($currency, 'Enabled');
    }

    /**
     * @param CurrencyInterface $currency
     * @param string $status
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    private function checkCurrencyStatus(CurrencyInterface $currency, $status)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['code' => $currency->getCode()]);
        $enabledField = $tableAccessor->getFieldFromRow($table, $row, 'enabled');

        return $enabledField->getText() === $status;
    }
}
