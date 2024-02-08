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

namespace Sylius\Behat\Page\Admin\Country;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Addressing\Model\CountryInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function isCountryDisabled(CountryInterface $country): bool
    {
        return $this->checkCountryStatus($country, 'Disabled');
    }

    public function isCountryEnabled(CountryInterface $country): bool
    {
        return $this->checkCountryStatus($country, 'Enabled');
    }

    private function checkCountryStatus(CountryInterface $country, string $status): bool
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['code' => $country->getCode()]);
        $enabledField = $tableAccessor->getFieldFromRow($table, $row, 'enabled');

        return $enabledField->getText() === $status;
    }
}
