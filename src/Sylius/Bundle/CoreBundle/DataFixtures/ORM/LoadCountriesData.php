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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\AddressingBundle\Model\CountryInterface;
use Symfony\Component\Locale\Locale;

/**
 * Default country fixtures.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadCountriesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $countryRepository = $this->getCountryRepository();
        //$countries = Locale::getDisplayCountries('pl');
        $countries = array('PL' => 'Polska');

        foreach ($countries as $isoName => $name) {
            $country = $countryRepository->createNew();

            $country->setName($name);
            $country->setIsoName($isoName);

            if ('PL' === $isoName) {
                $this->addPlProvince($country);
            }

            $manager->persist($country);

            $this->setReference('Sylius.Country.'.$isoName, $country);
        }

        $manager->flush();
    }

    /**
     * Adds all US states as provinces to given country.
     *
     * @param CountryInterface $country
     */
    private function addPlProvince(CountryInterface $country)
    {
        $states = array(
            'Łódzkie' => 'Łódzkie',
            'Świętokrzyskie' => 'Świętokrzyskie',
        );

        $provinceRepository = $this->getProvinceRepository();

        foreach ($states as $isoName => $name) {
            $province = $provinceRepository->createNew();
            $province->setName($name);

            $country->addProvince($province);

            $this->setReference('Sylius.Province.'.$isoName, $province);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
