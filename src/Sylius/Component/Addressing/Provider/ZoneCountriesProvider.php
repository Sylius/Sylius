<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ZoneCountriesProvider implements ZoneCountriesProviderInterface
{
    /** @var RepositoryInterface */
    private $zoneRepository;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $provinceRepository;

    public function __construct(
        RepositoryInterface $zoneRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository
    ) {
        $this->zoneRepository = $zoneRepository;
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountriesInWhichZoneOperates(ZoneInterface $zone): array
    {
        return array_values(array_unique($this->getZoneCountries($zone), SORT_REGULAR));
    }

    /**
     * @return array|CountryInterface[]
     */
    private function getZoneCountries(ZoneInterface $zone): array
    {
        $countries = [];

        if ($zone->getType() === ZoneInterface::TYPE_COUNTRY) {
            /** @var ZoneMemberInterface $zoneMember */
            foreach ($zone->getMembers() as $zoneMember) {
                $countries[] = $this->countryRepository->findOneBy(['code' => $zoneMember->getCode()]);
            }
        }

        if ($zone->getType() === ZoneInterface::TYPE_PROVINCE) {
            /** @var ZoneMemberInterface $province */
            foreach ($zone->getMembers() as $zoneMember) {
                /** @var ProvinceInterface $province */
                $province = $this->provinceRepository->findOneBy(['code' => $zoneMember->getCode()]);
                $countries[] = $province->getCountry();
            }
        }

        if ($zone->getType() === ZoneInterface::TYPE_ZONE) {
            /** @var ZoneMemberInterface $zoneMember */
            foreach ($zone->getMembers() as $zoneMember) {
                /** @var ZoneInterface $zone */
                $zone = $this->zoneRepository->findOneBy(['code' => $zoneMember->getCode()]);

                $countries = array_merge($countries, $this->getZoneCountries($zone));
            }
        }

        return $countries;
    }
}
