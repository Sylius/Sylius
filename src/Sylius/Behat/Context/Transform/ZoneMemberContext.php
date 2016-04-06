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
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
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
    private $provinceRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneMemberRepository;

    /**
     * @param CountryNameConverterInterface $countryNameConverter
     * @param RepositoryInterface $provinceRepository
     * @param RepositoryInterface $zoneRepository
     * @param RepositoryInterface $zoneMemberRepository
     */
    public function __construct(
        CountryNameConverterInterface $countryNameConverter,
        RepositoryInterface $provinceRepository,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $zoneMemberRepository
    ) {
        $this->countryNameConverter = $countryNameConverter;
        $this->provinceRepository = $provinceRepository;
        $this->zoneRepository = $zoneRepository;
        $this->zoneMemberRepository = $zoneMemberRepository;
    }

    /**
     * @Transform the :countryName country member
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
        $provinceCode = $this->getProvinceByName($name)->getCode();
        $provinceTypeZoneMember = $this->getZoneMemberByCode($provinceCode);

        return $provinceTypeZoneMember;
    }

    /**
     * @Transform the :name zone member
     */
    public function getZoneTypeZoneMemberByName($name)
    {
        $zoneCode = $this->getZoneByName($name)->getCode();
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
     *
     * @return ProvinceInterface
     */
    private function getProvinceByName($name)
    {
        $province = $this->provinceRepository->findOneBy(['name' => $name]);

        if (null === $province) {
            throw new \InvalidArgumentException(sprintf('Province with name %s does not exist.', $name));
        }

        return $province;
    }

    /**
     * @param string $name
     *
     * @return ZoneInterface
     */
    private function getZoneByName($name)
    {
        $zone = $this->zoneRepository->findOneBy(['name' => $name]);

        if (null === $zone) {
            throw new \InvalidArgumentException(sprintf('Zone with name %s does not exist.', $name));
        }

        return $zone;
    }
}
