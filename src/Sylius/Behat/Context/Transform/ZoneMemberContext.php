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
use Webmozart\Assert\Assert;

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
     * @Transform the :name country member
     */
    public function getCountryTypeZoneMemberByName($name)
    {
        $countryCode = $this->countryNameConverter->convertToCode($name);

        return $this->getZoneMemberByCode($countryCode);
    }

    /**
     * @Transform the :name province member
     */
    public function getProvinceTypeZoneMemberByName($name)
    {
        $provinceCode = $this->getProvinceByName($name)->getCode();

        return $this->getZoneMemberByCode($provinceCode);
    }

    /**
     * @Transform the :name zone member
     */
    public function getZoneTypeZoneMemberByName($name)
    {
        $zoneCode = $this->getZoneByName($name)->getCode();

        return $this->getZoneMemberByCode($zoneCode);
    }

    /**
     * @param string $code
     *
     * @return ZoneMemberInterface
     *
     * @throws \InvalidArgumentException
     */
    private function getZoneMemberByCode($code)
    {
        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $code]);
        Assert::notNull(
            $zoneMember,
            sprintf('Zone member with code %s does not exist.', $code)
        );

        return $zoneMember;
    }

    /**
     * @param string $name
     *
     * @return ProvinceInterface
     *
     * @throws \InvalidArgumentException
     */
    private function getProvinceByName($name)
    {
        $province = $this->provinceRepository->findOneBy(['name' => $name]);
        Assert::notNull(
            $province,
            sprintf('Province with name %s does not exist.', $name)
        );

        return $province;
    }

    /**
     * @param string $name
     *
     * @return ZoneInterface
     *
     * @throws \InvalidArgumentException
     */
    private function getZoneByName($name)
    {
        $zone = $this->zoneRepository->findOneBy(['name' => $name]);
        Assert::notNull(
            $zone,
            sprintf('Zone with name %s does not exist.', $name)
        );

        return $zone;
    }
}
