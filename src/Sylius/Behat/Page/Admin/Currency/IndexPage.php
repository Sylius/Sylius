<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Currency\Model\CurrencyInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function isCurrencyDisabled(CurrencyInterface $currency): bool
    {
        return $this->checkCurrencyStatus($currency, 'Disabled');
    }

    public function isCurrencyEnabled(CurrencyInterface $currency): bool
    {
        return $this->checkCurrencyStatus($currency, 'Enabled');
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function checkCurrencyStatus(CurrencyInterface $currency, string $status): bool
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['code' => $currency->getCode()]);
        $enabledField = $tableAccessor->getFieldFromRow($table, $row, 'enabled');

        return $enabledField->getText() === $status;
    }
}
