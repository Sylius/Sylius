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
use Sylius\Behat\Page\ElementNotFoundException;
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
        try {
            $tableManipulator = $this->getTableManipulator();
            $table = $this->getElement('table');

            $row = $tableManipulator->getRowWithFields($table, ['code' => $country->getCode()]);
            $enabledField = $tableManipulator->getFieldFromRow($table, $row, 'Enabled');

            return $enabledField->getText() === $status;
        } catch (\InvalidArgumentException $exception) {
            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }
}
