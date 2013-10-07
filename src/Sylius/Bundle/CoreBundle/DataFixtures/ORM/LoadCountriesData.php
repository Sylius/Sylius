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

use Nelmio\Alice\Fixtures;
use Symfony\Component\Intl\Intl;

/**
 * Default country fixtures.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class LoadCountriesData extends AbstractDataFixture
{
    public function getCountryName($iso)
    {
        return $this->getCountries()[$iso];
    }

    public function getCountries()
    {
        $locale = $this->container->getParameter('sylius.locale');
        return Intl::getRegionBundle()->getCountryNames($locale);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/../DATA/countries.yml',

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}