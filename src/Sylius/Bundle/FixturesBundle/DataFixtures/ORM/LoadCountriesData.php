<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Intl\Intl;

/**
 * Default country fixtures.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadCountriesData extends DataFixture
{
    private $states = array(
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District Of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $countryRepository = $this->getCountryRepository();
        $countries = Intl::getRegionBundle()->getCountryNames($this->defaultLocale);

        if (Intl::isExtensionLoaded()) {
            $localisedCountries = array('es_ES' => Intl::getRegionBundle()->getCountryNames('es_ES'));
        } else {
            $localisedCountries = array();
        }

        foreach ($countries as $isoName => $name) {
            $country = $countryRepository->createNew();

            $country->setCurrentLocale($this->defaultLocale);
            $country->setName($name);

            foreach ($localisedCountries as $locale => $translatedCountries) {
                $country->setCurrentLocale($locale);
                $country->setName($translatedCountries[$isoName]);
            }

            $country->setIsoName($isoName);

            if ('US' === $isoName) {
                $this->addUsStates($country);
            }

            $manager->persist($country);

            $this->setReference('Sylius.Country.'.$isoName, $country);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Adds all US states as provinces to given country.
     *
     * @param CountryInterface $country
     */
    protected function addUsStates(CountryInterface $country)
    {
        $provinceRepository = $this->getProvinceRepository();

        foreach ($this->states as $isoName => $name) {
            $province = $provinceRepository->createNew()
                ->setName($name)
                ->setIsoName($isoName)
            ;
            $country->addProvince($province);

            $this->setReference('Sylius.Province.'.$isoName, $province);
        }
    }
}
