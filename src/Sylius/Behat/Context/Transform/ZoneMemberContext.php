<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ZoneMemberContext implements Context
{
    /**
     * @var CountryNameConverterInterface
     */
    private $countryNameConverter;

    /**
     * @var RepositoryInterface
     */
    private $zoneMemberRepository;

    /**
     * @var array
     */
    private $provinces = [
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
     * @var array
     */
    private $zones = [
        'NA' => 'North America',
        'SA' => 'South America',
    ];

    /**
     * @param CountryNameConverterInterface $countryNameConverter
     * @param RepositoryInterface $zoneMemberRepository
     */
    public function __construct(
        CountryNameConverterInterface $countryNameConverter,
        RepositoryInterface $zoneMemberRepository
    ) {
        $this->countryNameConverter = $countryNameConverter;
        $this->zoneMemberRepository = $zoneMemberRepository;
    }

    /**
     * @Transform the :name country member
     */
    public function getCountryTypeZoneMemberByName($name)
    {
        $countryCode = $this->countryNameConverter->convertToCode($name);
        $countryTypeZoneMember = $this->getZoneMemberByCode($countryCode);

        return $countryTypeZoneMember;
    }

    /**
     * @Transform the :name province member
     */
    public function getProvinceTypeZoneMemberByName($name)
    {
        $provinceCode = $this->convertNameToCode($name, $this->provinces);
        $provinceTypeZoneMember = $this->getZoneMemberByCode($provinceCode);

        return $provinceTypeZoneMember;
    }

    /**
     * @Transform the :name zone member
     */
    public function getZoneTypeZoneMemberByName($name)
    {
        $zoneCode = $this->convertNameToCode($name, $this->zones);
        $zoneTypeZoneMember = $this->getZoneMemberByCode($zoneCode);

        return $zoneTypeZoneMember;
    }

    /**
     * @param string $code
     *
     * @return ZoneMemberInterface
     */
    private function getZoneMemberByCode($code)
    {
        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $code]);

        if (null === $zoneMember) {
            throw new \InvalidArgumentException(sprintf('Zone member with code %s does not exist.', $code));
        }

        return $zoneMember;
    }

    /**
     * @param string $name
     * @param array $codes
     *
     * @return string
     */
    private function convertNameToCode($name, array $codes)
    {
        $code = array_search($name, $codes, true);

        if (false === $code) {
            throw new \RuntimeException(sprintf('Cannot convert name %s to code', $name));
        }

        return $code;
    }
}
