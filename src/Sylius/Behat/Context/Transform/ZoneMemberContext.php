<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    public function __construct(
        private CountryNameConverterInterface $countryNameConverter,
        private RepositoryInterface $provinceRepository,
        private RepositoryInterface $zoneRepository,
        private RepositoryInterface $zoneMemberRepository,
    ) {
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
     * @Transform /^"([^"]+)", "([^"]+)" and "([^"]+)" country members$/
     * @Transform /^"([^"]+)" and "([^"]+)" country members$/
     */
    public function getCountryTypeZoneMembersByNames(string ...$names): array
    {
        $codes = $names;
        array_walk($codes, fn (&$item) => $item = $this->countryNameConverter->convertToCode($item));

        return $this->getZoneMembersByCodes($codes);
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
            sprintf('Zone member with code %s does not exist.', $code),
        );

        return $zoneMember;
    }

    private function getZoneMembersByCodes(array $codes): array
    {
        return $this->zoneMemberRepository->findBy(['code' => $codes]);
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
            sprintf('Province with name %s does not exist.', $name),
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
            sprintf('Zone with name %s does not exist.', $name),
        );

        return $zone;
    }
}
