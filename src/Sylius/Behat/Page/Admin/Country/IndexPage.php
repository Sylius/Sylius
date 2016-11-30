<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Country;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Addressing\Model\CountryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCountryDisabled(CountryInterface $country)
    {
        return $this->checkCountryStatus($country, 'Disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isCountryEnabled(CountryInterface $country)
    {
        return $this->checkCountryStatus($country, 'Enabled');
    }

    /**
     * @param CountryInterface $country
     * @param string $status
     *
     * @return bool
     */
    private function checkCountryStatus(CountryInterface $country, $status)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['code' => $country->getCode()]);
        $enabledField = $tableAccessor->getFieldFromRow($table, $row, 'enabled');

        return $enabledField->getText() === $status;
    }
}
