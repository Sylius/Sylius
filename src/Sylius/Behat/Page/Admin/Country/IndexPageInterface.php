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

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param CountryInterface $country
     *
     * @return bool
     */
    public function isCountryDisabled(CountryInterface $country);

    /**
     * @param CountryInterface $country
     *
     * @return bool
     */
    public function isCountryEnabled(CountryInterface $country);
}
