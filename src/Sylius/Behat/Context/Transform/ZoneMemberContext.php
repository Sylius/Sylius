<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

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
     * @throws \InvalidArgumentException
     */
    private function getZoneMemberByCode(string $code): ZoneMemberInterface
    {
        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $code]);
        Assert::notNull(
            $zoneMember,
            sprintf('Zone member with code %s does not exist.', $code)
        );

        return $zoneMember;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getProvinceByName(string $name): ProvinceInterface
    {
        $province = $this->provinceRepository->findOneBy(['name' => $name]);
        Assert::notNull(
            $province,
            sprintf('Province with name %s does not exist.', $name)
        );

        return $province;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getZoneByName(string $name): ZoneInterface
    {
        $zone = $this->zoneRepository->findOneBy(['name' => $name]);
        Assert::notNull(
            $zone,
            sprintf('Zone with name %s does not exist.', $name)
        );

        return $zone;
    }
}
