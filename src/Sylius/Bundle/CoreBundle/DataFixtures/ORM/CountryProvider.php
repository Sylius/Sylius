<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Symfony\Component\Intl\Intl;

/**
 * Additional country Faker provider
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class CountryProvider extends AbstractProvider
{
    /**
     * Return the name of the country according to its iso code.
     *
     * @param $isoCode
     * @return string
     */
    public function countryName($isoCode)
    {
        return $this->getCountries()[$isoCode];
    }

    /**
     * Return a user currency
     *
     * @return string
     */
    public function currency()
    {
        return $this->faker->randomElement(array('EUR', 'USD', 'GBP'));
    }

    /**
     * Return the list of countries.
     *
     * @return \string[]
     */
    public function getCountries()
    {
        return Intl::getRegionBundle()->getCountryNames($this->locale);
    }
}