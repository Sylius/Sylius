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
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class LoadCountriesData extends DataFixture
{
    protected $states = [
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
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $countryFactory = $this->getCountryFactory();
        $countries = Intl::getRegionBundle()->getCountryNames();

        foreach ($countries as $countryCode => $name) {
            $country = $countryFactory->createNew();

            $country->setCode($countryCode);

            if ('US' === $countryCode) {
                $this->addUsStates($country);
            }

            $manager->persist($country);

            $this->setReference('Sylius.Country.'.$countryCode, $country);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * Adds all US states as provinces to given country.
     *
     * @param CountryInterface $country
     */
    protected function addUsStates(CountryInterface $country)
    {
        $provinceFactory = $this->getProvinceFactory();
        $countryCode = $country->getCode();

        foreach ($this->states as $baseProvinceCode => $name) {
            $province = $provinceFactory->createNew();
            $province->setName($name);
            $newProvinceCode = sprintf('%s-%s', $countryCode, $baseProvinceCode);
            $province->setCode($newProvinceCode);
            $country->addProvince($province);

            $this->setReference('Sylius.Province.'.$newProvinceCode, $province);
        }
    }
}
