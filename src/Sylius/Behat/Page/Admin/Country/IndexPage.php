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
            $row = $this->getTableManipulator()->getRowWithFields($this->getElement('table'), ['code' => $country->getCode()]);

            return false !== strpos($row->getText(), $status);
        } catch (\InvalidArgumentException $exception) {
            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }
}
